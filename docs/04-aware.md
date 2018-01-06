# Aware Interfaces

There are two main interfaces that the `Caridea\Container\Objects` class supports when objects are instantiated.

## ContainerAware

If an object implements `Caridea\Container\ContainerAware`, it will have its `setContainer` method called as soon as it's instantiated.

A simple trait helps with this interface.

```php
class MyContainerAware implements \Caridea\Container\ContainerAware
{
    use \Caridea\Container\ContainerSetter;

    public function __construct()
    {
        // set the property with the no-op container instead of leaving it null.
        // it's a good habit!
        $this->container = new \Caridea\Container\EmptyContainer();
    }
}
```

## PublisherAware

If an object implements `Caridea\Event\PublisherAware`, it will have its `setPublisher` method called as soon as it's instantiated.

A simple trait helps with this interface.

```php
class MyPublisherAware implements \Caridea\Event\PublisherAware
{
    use \Caridea\Event\PublisherSetter;

    public function __construct()
    {
        // set the property with the no-op publisher instead of leaving it null.
        // it's a good habit!
        $this->setPublisher(new \Caridea\Event\NullPublisher());
    }
}
```
