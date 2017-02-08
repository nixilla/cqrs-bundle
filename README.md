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
