# QuillStack DI Container

[![Build Status](https://travis-ci.org/quillstack/di.svg?branch=master)](https://travis-ci.org/quillstack/di)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=quillstack_di&metric=alert_status)](https://sonarcloud.io/dashboard?id=quillstack_di)
[![Downloads](https://img.shields.io/packagist/dt/quillstack/di.svg)](https://packagist.org/packages/quillstack/di)
[![StyleCI](https://github.styleci.io/repos/291464853/shield?branch=master)](https://github.styleci.io/repos/291464853?branch=master)
[![CodeFactor](https://www.codefactor.io/repository/github/quillstack/di/badge)](https://www.codefactor.io/repository/github/quillstack/di)
[![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=quillstack_di&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=quillstack_di)
[![Lines of Code](https://sonarcloud.io/api/project_badges/measure?project=quillstack_di&metric=ncloc)](https://sonarcloud.io/dashboard?id=quillstack_di)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=quillstack_di&metric=coverage)](https://sonarcloud.io/dashboard?id=quillstack_di)
![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/quillstack/di)
![Packagist License](https://img.shields.io/packagist/l/quillstack/di)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/quillstack/di/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/quillstack/di/?branch=master)

QuillStack DI Container is the dependency injection container based
on _PSR-11: Container interface_, and with the main goal: to be fast.
You can find the full documentation on the website: \
https://quillstack.com/di

This DI container uses constructors and types of the class properties.

### Installation

To install this package, run the standard command using _Composer_:

```
composer require quillstack/di
```

### Usage

You can use Quill DI when you want:
- To have a simple and fast DI container.
- Define dependencies based on interfaces.
- Define parameters e.g. credentials for a database in `Database` class.
- To use constructors or/and class properties.
- To implement your own instance factories e.g. for `Request` classes.

#### Simple usage

You can easily start using a DI Container:

```php
<?php

use QuillStack\DI\Container;

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

If you want to define which class should be loaded based on an interface,
you can easily do that:

```php
$container = new Container([
    LoggerInterface::class => Logger::class,
]);
$controller = $container->get(ExampleController::class);
```

You can easily define your dependencies using interfaces:

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

When you create the object using DI container, the type of `$logger` property
will be set to `Logger`.

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

You can implement your own instance factory. If there a family of classes,
where you'd like to create a class in a special way, it'll be available.

In our example we want to create different request objects:

```php
<?php

class CreateUserRequest implements RequestInterface
{
}
```

First we had to create `RequestInterface` as a common interface for all
requests.

Next we have to create an instance factory class. To create it extend a class
with `CustomFactoryInterface`:

```php
<?php

use QuillStack\DI\CustomFactoryInterface;

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

Also, use this configuration array, when you create a DI contaienr:

```php
$container = new Container([
    RequestInterface::class => RequestClassFactory::class,
]);
$controller = $container->get(ExampleController::class);
```

### Benchmarks

There's a repository where you can see the benchmark results. You can
also do your own tests: \
https://github.com/quillstack/di-examples

Containers used in tests: Quill DI, Dice, PHP-DI, Symfony,
Laminas DI, Aura.Di. URLs and results for these solutions
you'll find in the repository above. 

### Unit tests

Run tests using a command:

```
phpdbg -qrr vendor/bin/phpunit
```

Check the tests coverage:

```
phpdbg -qrr vendor/bin/phpunit --coverage-html coverage tests
```

## Quill Stack

If you want to know more about other solutions, visit the website: \
https://quillstack.com/ 

[![The Quill Stack](http://quillstack.com/quillstack.png)](https://quillstack.com/)
