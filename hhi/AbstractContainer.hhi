<?hh // strict

namespace Caridea\Container;

abstract class AbstractContainer implements \Caridea\Container\Container
{
    protected ?\Caridea\Container\Container $parent;

    protected array<string,string> $types = [];

    protected static array<string> $primitives = ['array', 'bool', 'float', 'int', 'resource', 'string'];

    protected function __construct(array<string,string> $types, ?\Caridea\Container\Container $parent = null)
    {
    }
    
    public function contains(string $name): bool
    {
        return false;
    }
    
    public function containsType(string $type): bool
    {
        return false;
    }
    
    public function get(string $name): mixed
    {
        return null;
    }

    public function getByType<T>(classname<T> $type): array<string,T>
    {
        return [];
    }

    public function getFirst<T>(classname<T> $type): ?T
    {
        return null;
    }

    abstract protected function doGet(string $name): mixed;
    
    public function getNames(): array<string>
    {
        return [];
    }

    public function getParent(): ?\Caridea\Container\Container
    {
        return null;
    }

    public function getType(string $name): ?string
    {
        return null;
    }

    public function named<T>(string $name, classname<T> $type): T
    {
        throw new \Exception();
    }
}
