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

# Usage


