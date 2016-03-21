<?php
/**
 * Caridea
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 *
 * @copyright 2015-2016 LibreWorks contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache 2.0 License
 */
namespace Caridea\Container;

/**
 * Builder for dependency injection container.
 *
 * @copyright 2015-2016 LibreWorks contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache 2.0 License
 */
class Builder
{
    /**
     * @var Provider[] Associative array of string names to `Provider` instances
     */
    protected $providers = [];
    /**
     * @var string[] Array of eager `Provider` names
     */
    protected $eager = [];
    
    /**
     * Adds a new Provider.
     *
     * This method exists only for completeness. You'd probably spend less
     * effort if you call `->eager`, `->lazy`, or `->proto`.
     *
     * @param string $name The component name
     * @param \Caridea\Container\Provider $provider The provider
     * @return $this provides a fluent interface
     */
    public function addProvider($name, Provider $provider)
    {
        $this->providers[$name] = $provider;
        return $this;
    }
    
    /**
     * Adds a singleton component to be instantiated after the container is.
     *
     * ```php
     * $builder->eager('foobar', 'Acme\Mail\Service', function($c) {
     *     return new \Acme\Mail\Service($c['dependency']);
     * });
     * ```
     *
     * @param string $name The component name
     * @param string $type The class name of the component
     * @param object $factory A `Closure` or class with an `__invoke` method to return the component
     * @return $this provides a fluent interface
     */
    public function eager($name, $type, $factory)
    {
        $provider = new Provider($type, $factory);
        $this->eager[] = $name;
        return $this->addProvider($name, $provider);
    }
    
    /**
     * Adds a singleton component to be instantiated on demand.
     *
     * ```php
     * $builder->lazy('foobar', 'Acme\Mail\Service', function($c) {
     *     return new \Acme\Mail\Service($c['dependency']);
     * });
     * ```
     *
     * @param string $name The component name
     * @param string $type The class name of the component
     * @param object $factory A `Closure` or class with an `__invoke` method to return the component
     * @return $this provides a fluent interface
     */
    public function lazy($name, $type, $factory)
    {
        return $this->addProvider($name, new Provider($type, $factory));
    }
    
    /**
     * Adds a component that provides a new instance each time it's instantiated.
     *
     * ```php
     * $builder->lazy('objectStorage', 'SplObjectStorage', function($c) {
     *     return new \SplObjectStorage();
     * });
     * ```
     *
     * @param string $name The component name
     * @param string $type The class name of the component
     * @param object $factory A `Closure` or class with an `__invoke` method to return the component
     * @return $this provides a fluent interface
     */
    public function proto($name, $type, $factory)
    {
        return $this->addProvider($name, new Provider($type, $factory, false));
    }
    
    /**
     * Builds a container using the settings called.
     *
     * Any *eager* components will be instantiated at this time.
     *
     * When this method is called, this builder is reset to its default state.
     *
     * @param Container $parent An optional parent container
     * @return Objects The constructed `Objects` container
     */
    public function build(Container $parent = null)
    {
        $container = new Objects($this->providers, $parent);
        if (!empty($this->eager)) {
            foreach ($this->eager as $v) {
                $container->get($v);
            }
        }
        $this->providers = [];
        $this->eager = [];
        return $container;
    }
}
