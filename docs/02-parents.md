# Parent Delegation

Most container methods will look to the parent container for entries it might not have.

* `get` – If the current container doesn't have the provided name, it delegates to the parent
* `has` – If the current container doesn't have the provided name, it delegates to the parent
* `containsType` – If the current container doesn't have the provided type, it delegates to the parent
* `getByType` – First, all entries of the provided type from the parent are collected, and then any entries in the current container are added (if it so happens that entries have the same name in the parent, they are overwritten by the child)
* `getFirst` – If the current container doesn't have the provided type, it delegates to the parent
* `getNames` – This method does _not_ delegate
* `getParent` – Obviously, this method returns the parent if present
* `getType` – If the current container doesn't have the provided name, it delegates to the parent
* `named` – If the current container doesn't have the provided name, it delegates to the parent

Aside from system resources, there is no limit to the number of parents a container can have.

```php
$props = new \Caridea\Container\Properties([
    'db.host'      => 'example.com',
    'db.port'      => 1337,
    'db.user'      => 'dba',
]);
$backend = \Caridea\Container\Objects::builder()
    ->lazy('db', 'My\Db\Service', function ($c) {
        return new \My\Db\Service(
            $c->get('db.host'),
            $c->get('db.port'),
            $c->get('db.user')
        );
    })->build($props);
$frontend = \Caridea\Container\Objects::builder()
    ->lazy('controller', 'My\Web\Controller', function ($c) {
        return new \My\Web\Controller(
            $c->get('db')
        );
    })->build($backend);

$controller = $frontend->get('controller');
```
