# The Quill DI Container

[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=quillstack_di&metric=alert_status)](https://sonarcloud.io/dashboard?id=quillstack_di)
[![Downloads](https://img.shields.io/packagist/dt/quillstack/di.svg)](https://packagist.org/packages/quillstack/di)
[![StyleCI](https://github.styleci.io/repos/291464853/shield?branch=master)](https://github.styleci.io/repos/291464853?branch=master)
[![CodeFactor](https://www.codefactor.io/repository/github/quillstack/di/badge)](https://www.codefactor.io/repository/github/quillstack/di)
[![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=quillstack_di&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=quillstack_di)
[![Lines of Code](https://sonarcloud.io/api/project_badges/measure?project=quillstack_di&metric=ncloc)](https://sonarcloud.io/dashboard?id=quillstack_di)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=quillstack_di&metric=coverage)](https://sonarcloud.io/dashboard?id=quillstack_di)

The dependency injection container based on PSR-11: Container interface,
and with the main goal: to be fast.

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

#### Simple usage

You can easily start using a DI Container:

```php
<?php

use QuillStack\DI\Container;

require __DIR__ . '/../vendor/autoload.php';

$container = new Container();
$controller = $container->get(ExampleController::class);
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

#### Dependencies with parameters

If some of your classes require parameters, define them as an array
passed on the second parameter to the container:

```php
$container = new Container([], [
    Database::class => [
        'hostname' => 'localhost',
    ],
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

![The Quill Stack](http://quillstack.com/quillstack.png)
