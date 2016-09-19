<?hh // strict

namespace Caridea\Container;

interface ContainerAware
{
    public function setContainer(\Caridea\Container\Container $container): void;
}
