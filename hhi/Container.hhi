<?hh // strict

namespace Caridea\Container;

interface Container
{
    public function contains(string $name): bool;

    public function containsType(string $type): bool;

    public function get(string $name): mixed;

    public function getByType<T>(classname<T> $type): array<string,T>;
    
    public function getFirst<T>(classname<T> $type) : ?T;

    public function getNames(): array<int,string>;

    public function getParent(): ?\Caridea\Container\Container;

    public function getType(string $name): ?string;
}
