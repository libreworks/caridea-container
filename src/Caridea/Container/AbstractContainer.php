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

use Caridea\Reflect\Type;

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
     * @var \Caridea\Reflect\Type[] with string keys
     */
    protected $types = [];
    
    /**
     * Creates a new AbstractContainer.
     * 
     * @param \Caridea\Reflect\Type[] $types with string keys
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
        $realType = Type::get($type);
        foreach ($this->types as $ctype) {
            if ($realType->isSuperclassOf($ctype)) {
                return true;
            }
        }
        return $this->parent ? $this->parent->containsType($realType) : false;
    }
    
    public function get($name)
    {
        return isset($this->types[$name]) ? $this->doGet($name) :
            ($this->parent ? $this->parent->get($name) : null);
    }

    public function getByType($type)
    {
        $realType = Type::get($type);
        $components = $this->parent ? $this->parent->getByType($realType) : [];
        foreach ($this->types as $name => $ctype) {
            if ($realType->isSuperclassOf($ctype)) {
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
    protected abstract function doGet($name);
    
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
