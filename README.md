# CQRS/ES Bundle for Symfony which integrates Prooph library

This Symfony Bundle wires Prooph CQRS/ES library into Symfony project.

[![Build Status](https://travis-ci.org/nixilla/cqrs-bundle.svg?branch=master)](https://travis-ci.org/nixilla/cqrs-bundle)
[![Coverage Status](https://coveralls.io/repos/github/nixilla/cqrs-bundle/badge.svg)](https://coveralls.io/github/nixilla/cqrs-bundle)

## Installation

Install with composer:

```bash
composer require nixilla/cqrs-bundle
```

Add budle to AppKernel:

```php
<?php

// app/AppKernel.php

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            // other bundles here,
            new Nixilla\CqrsBundle\NixillaCqrsBundle()
        ];
        
        return $bundles;
    }
}
```

Configure store adapter. Here is an example of setting MongoDB adapter:

```yaml
# app/config/services.yml

parameters:

    mongodb_server: mongodb://localhost:27017
    mongodb_dbname: my_database

services:
    mongo.client:
        class: MongoClient
        arguments: [ "%mongodb_server%" ]
        
    mongo.event.store.adapter:
        class: Prooph\EventStore\Adapter\MongoDb\MongoDbEventStoreAdapter
        arguments: [ "@prooph.message.factory", "@prooph.message.converter", "@mongo.client", "%mongodb_dbname%" ]
```

```yaml
# app/config/config.yml

nixilla_cqrs:
    event_store:
        adapter: mongo.event.store.adapter

```

## Usage

CQRS/ES is quite simple and easy in development, but on the other hand quite time consuming. For each event you want
to record in the system, you need to create several classes. Below I'll show you how to create simple newsletter
signup event with this bundle.

Here is the list of CQRS/ES artifacts we will create in order

* CreateContact - command
* CreateContactHandler - command handler
* ContactRepository - class that interacts with data store - similar concept as Doctrine repository
* ContactCreated - this is the event class that gets persisted to data store

Let's start with non-cqrs stuff, and that is Symfony form and controller. I've omitted some code (like constructors and use statements)
to make it shorter.

```php
<?php

namespace AppBundle\Form;

class BasicContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('emailAddress', EmailType::class, [
                'constraints' => [ new NotBlank(), new Email() ]
            ])
        ;
    }
}
```

```php
<?php

namespace AppBundle\Controller;

class ContactCreateController
{
    /** @var Prooph\ServiceBus\CommandBus */
    private $commandBus;

    /** @var Symfony\Component\Form\FormInterface */
    private $form;

    public function createAction(Request $request)
    {
        $this->form->submit(json_decode($request->getContent(), true));

        if($this->form->isValid())
        {
            $this->commandBus->dispatch(new CreateContact($this->form->getData()));
            return new Response('', 201);
        }

        return new Response($this->form->getErrors(true)->__toString(), 422);
    }
}
```

The commandBus service is provided by this bundle, but CreateContact command is something we will created now.
This is the first CQRS artifact.

```php
<?php

namespace Newsletter\Domain\Command;

class CreateContact extends Prooph\Common\Messaging\Command
{
    private $payload;

    public function __construct(array $payload)
    {
        $this->init();
        $this->setPayload($payload);
    }

    public function payload() { return $this->payload; }

    protected function setPayload(array $payload) { $this->payload = $payload; }
}
```

Now if configure routing like this:

```yml
# src/AppBundle/Resources/config/routing.yml

contact_create:
    path: /contact
    defaults: { _controller: controller.contact.create:createAction }
    methods: [ POST ]
```

then you should be able to call it with curl like this:

```bash
curl -v \
    -H "Content-Type: application/json" \
    -X POST \
    --data '{"emailAddress":"john@smith.local"}' \
    http://localhost/contact
```
When you actually run this command now, you'll get exception `Message dispatch failed during locate-handler phase. Error: You have requested a non-existent service "handler.create_contact".`

The `handler.create_contact` is the service which we will create next. The only purpose for this service is to handle
`CreateContact` command. In other words, each command has only one command handler, and each command handler can only
handle one command.

```php
<?php

namespace Newsletter\Domain\CommandHandler;

class CreateContactHandler
{
    /** @var Newsletter\Domain\Repository\ContactRepositoryInterface */
    private $repository;

    public function __invoke(CreateContact $command)
    {
        $this->repository->add(Contact::fromPayload($command->payload()));
    }
}
```

This handler require 2 additional CQRS artifacts.

First one is ContactRepository which usually has 2 methods: `get` and `add`.

```php
<?php

namespace Newsletter\Domain\Repository;

interface ContactRepositoryInterface
{
    public function add(Newsletter\Domain\Aggregate\Contact $contact);
    public function get($id);
}

```

and implementation

```php
<?php

namespace AppBundle\Cqrs\Repository;

class ContactRepository implements Newsletter\Domain\Repository\ContactRepositoryInterface
{
    /** @var Prooph\EventStore\Aggregate\AggregateRepository */
    private $repository;

    public function add(Contact $contact) { $this->repository->addAggregateRoot($contact); }

    public function get($id) { return $this->repository->getAggregateRoot($id); }
}
```

Second one is event, which is recorded in data store. All events are in past tense. Good thing is that you don't
have to write any code - just extend base class.

```php
<?php
namespace Newsletter\Domain\Event;

use Prooph\EventSourcing\AggregateChanged;

class ContactCreated extends AggregateChanged
{
}
```

Now you need to let know Symfony how to construct service:

```yaml
# src/AppBundle/Resources/config/services.yml

parameters:

    form.contact.basic.type.class: AppBundle\Form\BasicContactType

services:

    aggregate.type.contact:
        class: Prooph\EventStore\Aggregate\AggregateType
        factory: [ 'Prooph\EventStore\Aggregate\AggregateType', 'fromAggregateRootClass']
        arguments: [ 'Newsletter\Domain\Aggregate\Contact' ]

    repository.aggregate.contact:
        class: Prooph\EventStore\Aggregate\AggregateRepository
        arguments: [ "@prooph.event.store", "@aggregate.type.contact", "@prooph.aggregate.translator" ]

    repository.document:
        class: AppBundle\Cqrs\Repository\ContactRepository
        arguments: [ "@repository.aggregate.contact" ]

    handler.create_contact:
        class: Newsletter\Domain\CommandHandler\CreateContactHandler
        arguments: [ "@repository.document" ]

    form.contact.basic.type:
        class: "%form.contact.basic.type.class%"
        tags:
            - { name: "form.type" }

    form.contact.basic:
        class: Symfony\Component\Form\FormInterface
        factory: [ "@form.factory", create ]
        arguments: [ "%form.contact.basic.type.class%" ]

    controller.contact.create:
        class: AppBundle\Controller\ContactCreateController
        arguments: [ "@prooph.command.bus", "@form.contact.basic" ]
```

Now if you run the curl command listed above, you should get HTTP 201 Created and you should see this record in your
MongoDB:

```json
{
    "_id" : "2d44331b-9d0d-47fa-b5be-01cd247c8a70",
    "version" : 1,
    "event_name" : "Newsletter\\Domain\\Event\\ContactCreated",
    "payload" : {
        "emailAddress" : "john@smith.local"
    },
    "created_at" : "2017-02-28T16:51:27.414000",
    "aggregate_id" : "john@smith.local",
    "aggregate_type" : "Newsletter\\Domain\\Aggregate\\Contact",
    "causation_id" : "21cd3129-0216-4437-86bc-9d7ede0bb08c",
    "causation_name" : "Newsletter\\Domain\\Command\\CreateContact"
}
```

Because there is a 1-to-1 relation between Command and CommandHandler, this bundle assumes following configuration:

* if command is called `\Any\Namespace\SomeCommand` then the service id that handles this command should be called `handler.some_command`
* namespace is ignored, only class name is used
* class name CamelCased is converted to snake_case using Symfony own `CamelCaseToSnakeCaseNameConverter`

## Listeners and Projectors

Although Prooph library does not distinguish listeners and projectors, this bundle supports this separation.

Why separate it? ES - Event Sourcing is a concept of storing data as series of events. At some point you will want to
replay all events and construct new read model. This may happen in various situations, for example when someone asks you:

* what is the newsletter monthly subscription frequency?
* or "Can you send this newsletter to all people why subscribed over the August only?"

The idea is that **listeners** are run only once, when the event actually occurs (real time).
For example, you may want to notify marketing team that they have new newsletter signup.
You don't want to execute listeners when you replay events to compile new read model or new report.

**Projectors** on the other hand, can be run as many time as you want. The purpose of the projector is update read model.
Updating read model could be:

* writing SQL commands to read database
* writing cache to Varnish
* writing data directly to ElasticSearch 
* building static HTML files 

A listener may look like this:

```php
<?php

namespace AppBundle\Cqrs\Listeners;

class MarketingNotificationListener
{
    /**
     * @var HipchatNotifier (from nixilla/hipchat-bundle)
     */
    private $notifier;
    
    public function __invoke(ContactCreated $event)
    {
        $payload = $event->payload();
        $message = sprintf("New Newsletter subscription, email address '%s'", $payload['emailAddress']);
        $this->notifier->notify('red', $message, 'text', true);
    }
}
```

and to make it work, you need to let know Symfony how to inject it.

```yaml

# src/AppBundle/Resources/config/services.yml

services:

    listener.account_name_mismatch:
        class: AppBundle\Cqrs\Listeners\MarketingNotificationListener
        arguments: [ "@hipchat.notifier" ]
        tags:
            - { name: cqrs.event.listener, event: Newsletter\Domain\Event\ContactCreated }

```

A single event can have many event listeners and many projectors. So in order to configure listener, you need
to tag service with `{ name: cqrs.event.listener, event: Newsletter\Domain\Event\ContactCreated }`
This will add listener to array of listener for given event class, and when even occurs, all listeners will be
executed.

