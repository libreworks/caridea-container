<?hh // strict

namespace Caridea\Container;

class Properties extends AbstractContainer
{
    public function __construct(array<string,mixed> $properties, ?\Caridea\Container\Container $parent = null)
    {
        parent::__construct([], $parent);
    }

    protected static function typeof(mixed $v): string
    {
        return '';
    }

    protected function doGet(string $name): mixed
    {
        return null;
    }
}
