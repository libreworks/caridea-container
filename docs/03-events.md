# Events

The `Caridea\Container\Objects` class is a concrete implementation of the `Caridea\Event\Publisher` interface. As detailed in the `caridea-event` documentation, classes which implement `Publisher` should send the event object provided to the `publish` method to any registered listeners.

Here is an example `Event` and `Listener`.

```php
namespace Foobar;

use Caridea\Event\Event;
use Caridea\Event\Listener;

class AuthenticationEvent extends Event
{
}

class AuthListener implements Listener
{
    public function notify(Event $event)
    {
        error_log('I got an event at ' . date('c', (int) $event->getWhen()));
    }
}
```

Now, let's create an `Objects` container and add the listener, making sure to register it as needing to be `eager`ly instantiated.

```php
use Caridea\Container\Objects;

$objects = Objects::builder()
    ->eager('foobarAuthListener', \Foobar\AuthListener, function ($c) {
        return new \Foobar\AuthListener();
    })->build();
```

Finally, we publish the event, and our listener receives it.

```php
class EventCreator
{
    public function create()
    {
        // all events need a "source" object
        return new \Foobar\AuthenticationEvent($this);
    }
}
$creator = new EventCreator();
$objects->publish($creator->create()); // the container calls our `notify` method, and it adds to the `error_log`
```

Listeners are called in defined order, and only start receiving events once actually created. That's why defining them as `eager` is important.
