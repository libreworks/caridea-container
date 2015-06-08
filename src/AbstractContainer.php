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
 * Abstract dependency injection container.
 *
 * @copyright 2015 LibreWorks contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache 2.0 License
 */
abstract class AbstractContainer implements Container
{
    /**
     * @var Container
     */
    protected $parent;
    /**
     * @var string[] with string keys
     */
    protected $types = [];
    /**
     * @var string[] the list of PHP native types
     */
    protected static $primatives = ['array', 'bool', 'float', 'int', 'resource', 'string'];
    
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
    
    public function contains($name)
    {
        return isset($this->types[$name]) ||
            ($this->parent ? $this->parent->contains($name) : false);
    }

    public function containsType($type)
    {
        if ($type === null) {
            return false;
        }
        $isObject = !in_array($type, self::$primatives, true);
        foreach ($this->types as $ctype) {
            if ($type === $ctype || ($isObject && is_a($ctype, $type, true))) {
                return true;
            }
        }
        return $this->parent ? $this->parent->containsType($type) : false;
    }
    
    public function get($name)
    {
        return isset($this->types[$name]) ? $this->doGet($name) :
            ($this->parent ? $this->parent->get($name) : null);
    }

    public function getByType($type)
    {
        if ($type === null) {
            return [];
        }
        $components = $this->parent ? $this->parent->getByType($type) : [];
        $isObject = !in_array($type, self::$primatives, true);
        foreach ($this->types as $name => $ctype) {
            if ($type === $ctype || ($isObject && is_a($ctype, $type, true))) {
                $components[$name] = $this->doGet($name);
            }
        }
        return $components;
    }
    
    /**
     * Retrieves the value
     *
     * @param string $name The value name
     */
    abstract protected function doGet($name);
    
    public function getNames()
    {
        return array_keys($this->types);
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function getType($name)
    {
        return isset($this->types[$name]) ? $this->types[$name] :
            ($this->parent ? $this->parent->getType($name) : null);
    }
}
