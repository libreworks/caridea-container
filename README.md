# caridea-container
Caridea is a miniscule PHP application library. This shrimpy fellow is what you'd use when you just want some helping hands and not a full-blown framework.

![](http://libreworks.com/caridea-100.png)

This is its dependency injection container. We included two containers.

The `Caridea\Container\Properties` class is intended for scalar configuration values that might be used as settings for other components.

The `Caridea\Container\Objects` class allows for eager, lazy, and prototype objects. It also implements `Caridea\Event\Publisher` and will broadcast events to any managed object which implements `Caridea\Event\Listener`.

You can retrieve contained objects both by name and by type!

[![Packagist](https://img.shields.io/packagist/v/caridea/container.svg)](https://packagist.org/packages/caridea/container)
[![Build Status](https://travis-ci.org/libreworks/caridea-container.svg)](https://travis-ci.org/libreworks/caridea-container)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/libreworks/caridea-container/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/libreworks/caridea-container/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/libreworks/caridea-container/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/libreworks/caridea-container/?branch=master)

We've also included `.hhi` files for the Hack typechecker.

## Installation

You can install this library using Composer:

```console
$ composer require caridea/container
```

* The master branch (version 2.x) of this project requires PHP 7.0 and depends on `caridea/event`.
* Version 1.x of this project requires PHP 5.5 and depends on `caridea/event`.

## Compliance

Releases of this library will conform to [Semantic Versioning](http://semver.org).

Our code is intended to comply with [PSR-1](http://www.php-fig.org/psr/psr-1/), [PSR-2](http://www.php-fig.org/psr/psr-2/), and [PSR-4](http://www.php-fig.org/psr/psr-4/). If you find any issues related to standards compliance, please send a pull request!

## Examples

Just a few quick examples.

### Configuration and Dependencies
```php
$config = [
    'db.uri' => 'mongodb://localhost:27017',
    'mail.host' => '192.168.1.100'
];
$properties = new \Caridea\Container\Properties($config);
$objects = \Caridea\Container\Objects::builder()
    ->eager('mongoClient', 'MongoClient', function($c){
        return new \MongoClient($c['db.uri']);
    })
    ->lazy('mailService', 'My\Mail\Service', function($c){
        return new \My\Mail\Service($c['mail.host']);
    })
    ->lazy('userService', 'My\User\Service', function($c){
        return new \My\User\Service($c['mongoClient'], $c['objectStorage']);
    })
    ->proto('objectStorage', 'SplObjectStorage', function($c){
        return new \SplObjectStorage();
    })
    ->build($properties);

$userService = $objects->get('userService');
```

* `\Caridea\Container\Properties` is meant to hold static, scalar properties.
* `\Caridea\Container\Objects` is meant to hold complex objects with dependencies.
* `\Caridea\Container\EmptyContainer` is an empty, no-op container.

### Parent Delegation

You can nest Objects containers. For example, you can have a container with service objects and a child container with web controllers.

```php
$services = \Caridea\Container\Objects::builder()
    ->eager('blogService', 'My\Blog\Service', function($c){
        return new \My\Blog\Service();
    })
    ->build();
$controllers = \Caridea\Container\Objects::builder()
    ->eager('blogController', 'My\Blog\Controller', function($c){
        return new \My\Blog\Controller($c['blogService']);
    })
    ->build($services);

$controllers = $controllers->getByType('My\Blog\Controller'); // ['blogController' => BlogController]
```

### Events

```php
$objects = \Caridea\Container\Objects::builder()
    ->eager('eventListener', 'My\Cool\EventListener', function($c){
        // we are assuming that this class implements Caridea\Event\Listener
        return new \My\Cool\EventListener();
    })
    ->build();

// assuming that CustomEvent implements Caridea\Event\Event
$objects->publish(new CustomEvent());
// Here, the eventListener object will have its ->notify() method invoked with the CustomEvent
```

Any objects returned from `get()` that implement `\Caridea\Event\PublisherAware` will receive the container via the `setPublisher` method.

### ContainerAware

Any objects returned from `get()` which implement `\Caridea\Container\ContainerAware` will receive the container via the `setContainer` method.
We provide a trait to make this easier.

```php
class MyContainerAware implements \Caridea\Container\ContainerAware
{
    use \Caridea\Container\ContainerSetter;

    public function __construct()
    {
        $this->container = new \Caridea\Container\EmptyContainer();
    }
}
```
