<?hh // strict

namespace Caridea\Container;

class Provider<T>
{
    // only here to make the type checker happy
    private classname<T> $type;
    private (function(\Caridea\Container\Container): T) $factory;

    public function __construct(classname<T> $type, (function(\Caridea\Container\Container): T) $factory, bool $singleton = true)
    {
        $this->type = $type;
        $this->factory = $factory;
    }

    public function get(\Caridea\Container\Container $container): T
    {
        $f = $this->factory;
        return $f($container);
    }
    
    public function isSingleton(): bool
    {
        return false;
    }

    public function getType(): classname<T>
    {
        return $this->type;
    }
}
