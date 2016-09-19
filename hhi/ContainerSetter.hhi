<?hh // strict

namespace Caridea\Container;

trait ContainerSetter
{
    protected ?\Caridea\Container\Container $container;

    public function setContainer(\Caridea\Container\Container $container): void
    {
        $this->container = $container;
    }
}
