# Dependency Injection Container

The dependency injection container based on PSR-11: Container interface,
and with the main goal: to be fast.

### Installation

To install this package, run the standard command using _Composer_:

```
composer require quillguild/dependency-injection
```

### Usage

You can easily start using a DI Container:

```php
<?php

use DependencyInjectionExample\ExampleController;
use QuillDI\Container;

require __DIR__ . '/../vendor/autoload.php';

$container = new Container();
$controller = $container->get(ExampleController::class);
```

### Benchmarks

There's a repository where you can see the benchmark results. You can
also do your own tests: \
https://github.com/quillguild/dependency-injection-example

Containers used in these tests:
- Quill Dependency Injection Container
- PHP-DI
- Dice