# Types of Containers

This library includes an interface, `Caridea\Container\Container`, which extends the PSR-11 container interface, `Psr\Container\ContainerInterface`. The following methods are required by the PSR interface.

* `get` – Loads an object by name, throws a `Caridea\Container\Exception\Missing` if there is no entry for the provided name
* `has` – Returns `true` if the container has an entry for the provided name

The following methods are required by the `Caridea\Container\Container` interface.

* `contains` – An alias for `has`, left for backward compatibility reasons
* `containsType` – Returns `true` if the container has an entry of the provided type
* `getByType` – Returns an `array` of entries of the provided type, keyed by name
* `getFirst` – Returns the first entry found of the provided type, or `null` if there are none
* `getNames` – Returns an `array` of all entry names in this container
* `getParent` – Returns the parent `Container` or `null` if there is no parent
* `getType` – Returns the type of the entry for the provided name, or `null` if there is no such entry
* `named` – Loads an object by name, throwing an `UnexpectedValueException` if the entry is not of the provided type

## Empty

There is a no-operation container, `Caridea\Container\EmptyContainer`, it works similarly to a container with no entries at all. It's useful for unit testing, and to use as a null object.

## Properties

The `Caridea\Container\Properties` container is meant to hold scalar configuration values that might be used as settings for other components. For example, database connection settings, SMTP server credentials, or directory locations in the file system.

This container is able to store not only scalar values, but instantiated objects as well.

```php
$props = new \Caridea\Container\Properties([
    'db.host'      => 'example.com',
    'db.port'      => 1337,
    'db.user'      => 'dba',
    'mail.host'    => 'smtp.example.net',
    'cache.holder' => new \SplObjectStorage(),
    'dates.nye'    => new \DateTime('2018-12-31')
]);
$props->getType('db.host'); // string
$props->getType('cache.holder'); // SplObjectStorage
```

## Objects

Even though `Caridea\Container\Properties` can store objects, it's not suited for it. The `Caridea\Container\Objects` container supports only objects and allows you to specify whether those objects are created only once (eager or lazy) or once for each time they are requested.

Instead of requiring a developer to adhere to some complex configuration in order to create your objects and their dependencies, the `Caridea\Container\Objects` class simply accepts an anonymous function that factories your object.

In the following example, we use the static method `builder()` to return a `Caridea\Container\Builder` that lets us define our object graph. We specify the `$props` container we created above as the parent.

```php
$objects = \Caridea\Container\Objects::builder()
    ->lazy('mailService', 'My\Mail\Service', function ($c) {
        return new \My\Mail\Service($c->get('mail.host'));
    })
    ->proto('userService', 'My\User\Service', function ($c) {
        return new \My\User\Service($c->get('mailService'));
    })
    ->build($props);
$objects->getType('userService'); // My\User\Service
$objects->get('db.host'); // example.com
```

As you can see, the anonymous function that creates your objects is passed the container, which you can use to lookup dependencies by name or by type.

### Singletons and Prototypes

Using the `Builder`, you can specify one of three instantiation types when you define a container entry.

* `lazy` – A lazy singleton. The factory method will be invoked only once, and only when the entry is first requested.
* `eager` – An eager singleton. The factory method will be invoked only once, and it will be called the instant the `build` method is invoked. This is useful for event listeners, as detailed in [chapter three](03-events.md).
* `proto` – The factory method will be called each time the entry is requested.

### Under the Hood

The `Caridea\Container\Builder` and `Caridea\Container\Objects` classes use the `Caridea\Container\Provider` class internally to manage entries and their instantiation behavior.
