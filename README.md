# Quillstack DI Container

[![Tests](https://github.com/quillstack/di/actions/workflows/tests.yml/badge.svg)](https://github.com/quillstack/di/actions/workflows/tests.yml)
[![Latest Version](https://img.shields.io/packagist/v/quillstack/di.svg)](https://packagist.org/packages/quillstack/di)
[![Downloads](https://img.shields.io/packagist/dt/quillstack/di.svg)](https://packagist.org/packages/quillstack/di)
[![PHP Version](https://img.shields.io/packagist/php-v/quillstack/di)](https://packagist.org/packages/quillstack/di)
[![StyleCI](https://github.styleci.io/repos/303510748/shield?branch=main)](https://github.styleci.io/repos/303510748?branch=main)
[![CodeFactor](https://www.codefactor.io/repository/github/quillstack/di/badge)](https://www.codefactor.io/repository/github/quillstack/di)
[![Quality Gate](https://sonarcloud.io/api/project_badges/measure?project=quillstack_di&metric=alert_status)](https://sonarcloud.io/summary/new_code?id=quillstack_di)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=quillstack_di&metric=coverage)](https://sonarcloud.io/summary/new_code?id=quillstack_di)
[![Maintainability](https://sonarcloud.io/api/project_badges/measure?project=quillstack_di&metric=sqale_rating)](https://sonarcloud.io/summary/new_code?id=quillstack_di)
[![Reliability](https://sonarcloud.io/api/project_badges/measure?project=quillstack_di&metric=reliability_rating)](https://sonarcloud.io/summary/new_code?id=quillstack_di)
[![Security](https://sonarcloud.io/api/project_badges/measure?project=quillstack_di&metric=security_rating)](https://sonarcloud.io/summary/new_code?id=quillstack_di)
[![License](https://img.shields.io/packagist/l/quillstack/di)](https://github.com/quillstack/di/blob/main/LICENSE)

A PSR-11 container which reads what a class needs from the class, and is built to do it quickly.

## Why this exists

A container has two jobs and they pull against each other: work out what a class needs, and hand
it over fast. Most containers answer that by making you choose — write the wiring out by hand
and it is fast, let it work things out and it is slow. The ones that give you both compile a
container to a PHP file, which is another build step, another cache to invalidate, and another
thing that is stale in development.

This one reads a constructor and remembers what it read. **Nothing is compiled and nothing is
written to disk**, and building a four-deep object graph from cold takes under a millisecond —
see the [benchmark](#benchmark).

It is also the container this framework runs on, which is the reason it exists at all: every
other package here can be built by hand, without any container, and none of them needs this one.
A container that packages depend on is a framework wearing a container's clothes.

## Requirements

- PHP 8.1 or newer

## Installation

```shell
composer require quillstack/di
```

## Usage

Nothing is registered. A class is asked for, and what its constructor declares is worked out:

```php
use Quillstack\DI\Container;

final class ExampleController
{
    public function __construct(private Database $database)
    {
    }
}
```

```php
$controller = (new Container())->get(ExampleController::class);
// App\ExampleController, with its Database already there
```

Public typed properties are filled too, which is how a class asks for something it does not want
in its constructor.

### Interfaces

Say once which class answers to an interface:

```php
$container = new Container([
    Storage::class => FileStorage::class,
]);

$container->get(StorageController::class)->storage;   // App\FileStorage
```

### Parameters

Where a class needs a value rather than an object, name it:

```php
$container = new Container([
    Database::class => ['hostname' => 'localhost'],
]);
```

```php
final class Database
{
    public function __construct(private string $hostname)
    {
    }
}
```

```php
$container->get(ExampleController::class)->database->hostname;   // 'localhost'
```

### An object you already have

Where something is built once at boot and used everywhere, hand the object over:

```php
use Psr\Log\LoggerInterface;
use Quillstack\Logger\Logger;

$logger = new Logger();

$container = new Container([
    LoggerInterface::class => $logger,
]);

$container->get(LoggingController::class)->logger === $logger;   // true
```

### Your own factory

Where a family of objects is built the same way — requests, reports, messages — write the
factory and let the container use it:

```php
use Quillstack\DI\Container;
use Quillstack\DI\CustomFactoryInterface;

final class ReportFactory implements CustomFactoryInterface
{
    private Container $container;

    public function setContainer(Container $container): self
    {
        $this->container = $container;

        return $this;
    }

    public function create(string $id): object
    {
        return new SalesReport($id);
    }
}
```

```php
$container = new Container([
    Report::class => ReportFactory::class,
]);

$container->get(ReportController::class)->report;   // App\SalesReport
```

`create()` is given the id that was asked for, which is what lets one factory serve a whole
family.

### Asking whether it can

```php
$container->has(Database::class);   // true
$container->has('nope');            // false
```

## Benchmark

Measured with [quillstack/benchmark](https://github.com/quillstack/benchmark) on **one object
graph four deep** — a controller needing a service and a repository, the service needing the
repository and a clock, the repository needing a connection. All four containers build the same
graph. Runs are interleaved, each figure is the median of five, and PHP is 8.5.7.

| | Version |
| --- | --- |
| quillstack/di | 0.6.0 |
| php-di/php-di | 7.1.1 |
| league/container | 4.2.5 |
| symfony/dependency-injection | v7.4.17 |

**A container built and the graph resolved, in a fresh process** — which is what a PHP request
does:

| | Time | Relative |
| --- | --- | --- |
| **quillstack/di** | **0.89 ms** | — |
| symfony/dependency-injection, compiled and dumped | 1.38 ms | 1.6× |
| league/container | 2.07 ms | 2.3× |
| php-di/php-di | 2.39 ms | 2.7× |
| symfony/dependency-injection, compiling each time | 15.65 ms | 17.6× |

The last row is the same container without its dump: Symfony compiles to a PHP file which is
normally written once at deploy, so the row above it is the fair one. It is in the table because
a container which has to be compiled is a build step this one does not have — in development,
that 15 ms is what a changed class costs.

**Asking again for something already built**, a thousand times over:

| | Per call |
| --- | --- |
| symfony/dependency-injection, dumped | 32 ns |
| php-di/php-di | 40 ns |
| **quillstack/di** | **53 ns** |
| league/container | 669 ns |

**This one is not the fastest here, and the gap is not worth having.** All four are looking a
key up in an array; 21 nanoseconds is nothing an application will feel, and the number that
decides a request is the one above.

## Tests

```shell
composer test
composer stan
```

## The rest of Quillstack

This is one component of [Quillstack](https://github.com/quillstack), a PHP framework which is
as simple to use as it is strict about what it does.

- [quillstack/framework](https://github.com/quillstack/framework) — what this wires together
- [quillstack/cli](https://github.com/quillstack/cli) — commands built the same way
- [quillstack/unit-tests](https://github.com/quillstack/unit-tests) — assertions arrive through a constructor too

## License

MIT — see [LICENSE](https://github.com/quillstack/di/blob/main/LICENSE).
