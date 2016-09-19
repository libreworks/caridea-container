<?hh // strict

namespace Caridea\Container;

class EmptyContainer implements \Caridea\Container\Container
{
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

    public function getFirst<T>(classname<T> $type) : ?T
    {
        return null;
    }

    public function getNames(): array<int,string>
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
        throw new \UnexpectedValueException("A $type was requested, but null was found");
    }
}
