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
 * Abstract dependency injection container.
 *
 * @copyright 2015-2016 LibreWorks contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache 2.0 License
 */
abstract class AbstractContainer implements Container
{
    /**
     * @var Container The parent container
     */
    protected $parent;
    /**
     * @var string[] with string keys
     */
    protected $types = [];
    /**
     * @var string[] the list of PHP native types
     */
    protected static $primitives = ['array', 'bool', 'float', 'int', 'resource', 'string'];
    
    /**
     * Creates a new AbstractContainer.
     *
     * @param string[] $types with string keys
     * @param \Caridea\Container\Container $parent The parent container
     */
    protected function __construct(array $types, Container $parent = null)
    {
        $this->types = $types;
        $this->parent = $parent;
    }
    
    /**
     * Whether this container or its parent contains a component with the given name.
     *
     * @param string $name The component name
     * @return bool
     */
    public function contains($name)
    {
        return isset($this->types[$name]) ||
            ($this->parent ? $this->parent->contains($name) : false);
    }
    
    /**
     * Whether this container or its parent contains a component with the given type.
     *
     * @param string $type The name of a class or one of PHP's language types
     *     (i.e. bool, int, float, string, array, resource)
     * @return bool
     */
    public function containsType($type)
    {
        if ($type === null) {
            return false;
        }
        $isObject = !in_array($type, self::$primitives, true);
        foreach ($this->types as $ctype) {
            if ($type === $ctype || ($isObject && is_a($ctype, $type, true))) {
                return true;
            }
        }
        return $this->parent ? $this->parent->containsType($type) : false;
    }
    
    /**
     * Gets a component by name.
     *
     * If this container doesn't have a value for that name, it will delegate to
     * its parent.
     *
     * @param string $name The component name
     * @return mixed The component or null if the name isn't registered
     */
    public function get($name)
    {
        return isset($this->types[$name]) ? $this->doGet($name) :
            ($this->parent ? $this->parent->get($name) : null);
    }

    /**
     * Gets the components in the contanier for the given type.
     *
     * The parent container is called first, then any values of this container
     * are appended to the array. Values in this container supercede any with
     * duplicate names in the parent.
     *
     * @param string $type The name of a class or one of PHP's language types
     *     (i.e. bool, int, float, string, array, resource)
     * @return array keys are component names, values are components themselves
     */
    public function getByType($type)
    {
        if ($type === null) {
            return [];
        }
        $components = $this->parent ? $this->parent->getByType($type) : [];
        $isObject = !in_array($type, self::$primitives, true);
        foreach ($this->types as $name => $ctype) {
            if ($type === $ctype || ($isObject && is_a($ctype, $type, true))) {
                $components[$name] = $this->doGet($name);
            }
        }
        return $components;
    }

    /**
     * Gets the first compenent found by type.
     *
     * If this container doesn't have a value of the type, it will delegate to
     * its parent.
     *
     * @param string $type The name of a class or one of PHP's language types
     *     (i.e. bool, int, float, string, array, resource)
     * @return mixed The component or null if one isn't registered
     */
    public function getFirst($type)
    {
        if ($type === null) {
            return null;
        }
        $isObject = !in_array($type, self::$primitives, true);
        foreach ($this->types as $name => $ctype) {
            if ($type === $ctype || ($isObject && is_a($ctype, $type, true))) {
                return $this->doGet($name);
            }
        }
        return $this->parent ? $this->parent->getFirst($type) : null;
    }

    /**
     * Retrieves the value
     *
     * @param string $name The value name
     */
    abstract protected function doGet($name);
    
    /**
     * Gets all registered component names (excluding any in the parent container).
     *
     * @return array of strings
     */
    public function getNames()
    {
        return array_keys($this->types);
    }

    /**
     * Gets the parent container.
     *
     * @return Container
     */
    public function getParent()
    {
        return $this->parent;
    }
    
    /**
     * Gets the type of component with the given name.
     *
     * If this container doesn't have a value for that name, it will delegate to
     * its parent.
     *
     * @param string $name The component name
     * @return string The component type, either a class name or one of PHP's language types
     *     (i.e. bool, int, float, string, array, resource)
     */
    public function getType($name)
    {
        return isset($this->types[$name]) ? $this->types[$name] :
            ($this->parent ? $this->parent->getType($name) : null);
    }
}
