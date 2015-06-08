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
 * @copyright 2015 LibreWorks contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache 2.0 License
 */
namespace Caridea\Container;

/**
 * Dependency injection instance provider.
 *
 * @copyright 2015 LibreWorks contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache 2.0 License
 */
class Provider
{
    /**
     * @var boolean
     */
    private $singleton = true;
    /**
     * @var string
     */
    private $type;
    /**
     * @var mixed
     */
    private $instance;
    /**
     * @var callable
     */
    private $factory;
    
    /**
     * @internal
     */
    public function __construct($type, $factory, $singleton = true)
    {
        if (!class_exists($type)) {
            throw new \InvalidArgumentException('"type" parameter must be a class that exists');
        }
        $this->type = $type;
        if (!is_object($factory) || !method_exists($factory, '__invoke')) {
            throw new \InvalidArgumentException('"factory" parameter must be a Closure or an object with an __invoke method');
        }
        $this->factory = $factory;
        $this->singleton = boolval($singleton);
    }
    
    /**
     * Gets the value instance.
     *
     * @param \Caridea\Container\Container $container The owning container
     * @return mixed The value instance
     */
    public function get(Container $container)
    {
        if ($this->singleton) {
            if ($this->instance === null) {
                $f = $this->factory;
                $this->instance = $f($container);
            }
            return $this->instance;
        } else {
            $f = $this->factory;
            return $f($container);
        }
    }
    
    /**
     * @return boolean whether this provider always returns the same value
     */
    public function isSingleton()
    {
        return $this->singleton;
    }

    /**
     * @return string gets the type of this instance
     */
    public function getType()
    {
        return $this->type;
    }
}
