# Quillstack DI Container

[![Build Status](https://app.travis-ci.com/quillstack/di.svg?branch=main)](https://app.travis-ci.com/quillstack/di)
[![Downloads](https://img.shields.io/packagist/dt/quillstack/di.svg)](https://packagist.org/packages/quillstack/di)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=quillstack_di&metric=coverage)](https://sonarcloud.io/dashboard?id=quillstack_di)
[![Lines of Code](https://sonarcloud.io/api/project_badges/measure?project=quillstack_di&metric=ncloc)](https://sonarcloud.io/dashboard?id=quillstack_di)
[![StyleCI](https://github.styleci.io/repos/291464853/shield?branch=main)](https://github.styleci.io/repos/291464853?branch=main)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/quillstack/di/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/quillstack/di/?branch=main)
[![CodeFactor](https://www.codefactor.io/repository/github/quillstack/di/badge)](https://www.codefactor.io/repository/github/quillstack/di)
![Packagist License](https://img.shields.io/packagist/l/quillstack/di)
[![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=quillstack_di&metric=reliability_rating)](https://sonarcloud.io/dashboard?id=quillstack_di)
[![Maintainability](https://api.codeclimate.com/v1/badges/d3657982e8a5bb50f4e3/maintainability)](https://codeclimate.com/github/quillstack/di/maintainability)
[![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=quillstack_di&metric=security_rating)](https://sonarcloud.io/dashboard?id=quillstack_di)
![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/quillstack/di)

Quillstack DI Container is the dependency injection container based
on _PSR-11: Container interface_, and with the main goal: to be fast.
You can find the full documentation on the website: \
https://quillstack.org/di

This DI container uses constructors and types of class properties.

### Installation

To install this package, run the standard command using _Composer_:

```
composer require quillstack/di
```

### Usage

You can use Quillstack DI Container when you want:
- To have a simple and fast DI container.
- Define dependencies based on interfaces.
- Define parameters e.g. credentials for a database in the `Database` class.
- To use constructors or/and class properties.
- To implement your own instance factories e.g. for `Request` classes.
- To use objects as dependencies.

#### Simple usage

You can easily start using a DI Container:

```php
<?php

use Quillstack\DI\Container;

require __DIR__ . '/../vendor/autoload.php';

$container = new Container();
$controller = $container->get(ExampleController::class);
```

Where your `ExampleController` class looks like:

```php
<?php

class ExampleController
{
    private $example = 3;
}
```

#### Dependencies based on interfaces

If you want to define which class should be loaded based on an interface:

```php
$container = new Container([
    LoggerInterface::class => Logger::class,
]);
$controller = $container->get(ExampleController::class);
```

You can define your dependencies using interfaces:

```php
<?php

class ExampleController
{
    public function __construct(
        private LoggerInterface $logger
    ) {
    }
}
```

When you create the object using the DI container, the type of `$logger` property will be set to `Logger`.

#### Dependencies with parameters

If some of your classes require parameters, define them as an array
passed on the second parameter to the container:

```php
$container = new Container([
    Database::class => [
        'hostname' => 'localhost',
    ],
]);
$controller = $container->get(ExampleController::class);
```

Every time you will get a database object, a container will use `localhost` as
a value for `$hostname` parameter:

```phpt
<?php

class Database
{
    public function __construct(
        private string $hostname
    ) {
    }
}
```

#### Custom instance factories

You can implement your own instance factory. This is especially useful when you want to create many objects in a class
family that are very similar in some way.

In our example we want to create different request objects:

```php
<?php

class CreateUserRequest implements RequestInterface
{
}
```

First, we had to create `RequestInterface` as a common interface for all requests.

Next, we have to create an instance factory class. To create it, extend a class with `CustomFactoryInterface`:

```php
<?php

use Quillstack\DI\CustomFactoryInterface;

class RequestClassFactory implements CustomFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function create(string $id): object
    {
        $factory = $this->container->get(GivenRequestFromGlobalsFactory::class);

        return $factory->createGivenServerRequest($id);
    }
}
```

Also, use this configuration array when you create a DI container:

```php
$container = new Container([
    RequestInterface::class => RequestClassFactory::class,
]);
$controller = $container->get(ExampleController::class);
```

Custom factories are useful for objects you want to create similarly.

#### Dependencies as objects

In this example, whenever a new class of LoggerInterface will be required as a dependency, a container will use a 
previously defined object. This object can be created once in a bootstrap file and used in the entire application:

```php
$logger = new Logger('name');
$logger->pushHandler(new StreamHandler('var/app.log'););

$container = new Container([
    LoggerInterface::class => $logger,
]);
```

This configuration is helpful if an object should be created once and its instance
should be used in other places in the application.

### Unit tests

Run tests using a command:

```
phpdbg -qrr ./vendor/bin/unit-tests
```

### Docker

```shell
$ docker-compose up -d
$ docker exec -w /var/www/html -it quillstack_di sh
```
