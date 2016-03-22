<?hh // strict

namespace Caridea\Container;

class Objects extends AbstractContainer implements \Caridea\Event\Publisher
{
    public function __construct<T>(array<Provider<T>> $providers, ?\Caridea\Container\Container $parent = null)
    {
        parent::__construct([], $parent);
    }
    
    public static function builder(): Builder
    {
        return new Builder();
    }
    
    /**
     * Retrieves the value
     *
     * @param string $name The value name
     */
    protected function doGet(string $name): mixed
    {
        return null;
    }

    public function publish<T>(\Caridea\Event\Event<T> $event): void
    {
    }
}
