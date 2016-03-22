<?hh // strict

namespace Caridea\Container;

class Builder
{
    public function addProvider<T>(string $name, Provider<T> $provider): this
    {
        return $this;
    }

    public function eager<T>(string $name, classname<T> $type, (function(\Caridea\Container\Container) : T) $factory): this
    {
        return $this;
    }

    public function lazy<T>(string $name, classname<T> $type, (function(\Caridea\Container\Container) : T) $factory): this
    {
        return $this;
    }

    public function proto<T>(string $name, classname<T> $type, (function(\Caridea\Container\Container) : T) $factory): this
    {
        return $this;
    }

    public function build(?\Caridea\Container\Container $parent = null): Objects
    {
        return new Objects([]);
    }
}
