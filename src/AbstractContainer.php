<?php
declare(strict_types=1);
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
 * @copyright 2015-2018 LibreWorks contributors
 * @license   Apache-2.0
 */
namespace Caridea\Container;

/**
 * Abstract dependency injection container.
 *
 * @copyright 2015-2018 LibreWorks contributors
 * @license   Apache-2.0
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
     * {@inheritDoc}
     */
    public function contains(string $name): bool
    {
        return $this->has($name);
    }

    /**
     * {@inheritDoc}
     */
    public function containsType(string $type): bool
    {
        $isObject = !in_array($type, self::$primitives, true);
        foreach ($this->types as $ctype) {
            if ($type === $ctype || ($isObject && is_a($ctype, $type, true))) {
                return true;
            }
        }
        return $this->parent ? $this->parent->containsType($type) : false;
    }

    /**
     * {@inheritDoc}
     */
    public function get($id)
    {
        $entry = isset($this->types[$id]) ? $this->doGet($id) :
            ($this->parent ? $this->parent->get($id) : null);
        if ($entry === null) {
            throw new Exception\Missing("No container entry found for key: $id");
        }
        return $entry;
    }

    /**
     * {@inheritDoc}
     */
    public function getByType(string $type): array
    {
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
     * {@inheritDoc}
     */
    public function getFirst(string $type)
    {
        $isObject = !in_array($type, self::$primitives, true);
        foreach ($this->types as $name => $ctype) {
            if ($type === $ctype || ($isObject && is_a($ctype, $type, true))) {
                return $this->doGet($name);
            }
        }
        return $this->parent ? $this->parent->getFirst($type) : null;
    }

    /**
     * {@inheritDoc}
     */
    public function has($name): bool
    {
        return isset($this->types[$name]) ||
            ($this->parent ? $this->parent->has($name) : false);
    }

    /**
     * Retrieves the value
     *
     * @param string $name The value name
     */
    abstract protected function doGet(string $name);

    /**
     * Gets all registered component names (excluding any in the parent container).
     *
     * @return array of strings
     */
    public function getNames(): array
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
    public function getType(string $name)
    {
        return isset($this->types[$name]) ? $this->types[$name] :
            ($this->parent ? $this->parent->getType($name) : null);
    }

    /**
     * Gets a component by name and ensures its type.
     *
     * If this container doesn't have a value for that name, it will delegate to
     * its parent.
     *
     * If the value isn't an instance of the type provided, an exception is
     * thrown, including when the value is `null`.
     *
     * @param string $name The component name
     * @param string $type The expected type
     * @return mixed The type-checked component
     * @throws \UnexpectedValueException if the `$type` doesn't match the value
     */
    public function named(string $name, string $type)
    {
        if (isset($this->types[$name])) {
            $ctype = $this->types[$name];
            $isObject = !in_array($type, self::$primitives, true);
            if ($type !== $ctype && (!$isObject || !is_a($ctype, $type, true))) {
                throw new \UnexpectedValueException("A $type was requested, but a $ctype was found");
            }
            return $this->doGet($name);
        } elseif ($this->parent !== null) {
            return $this->parent->named($name, $type);
        }
        throw new \UnexpectedValueException("A $type was requested, but null was found");
    }
}
