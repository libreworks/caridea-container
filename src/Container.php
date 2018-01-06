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
 * Dependency injection container.
 *
 * @copyright 2015-2018 LibreWorks contributors
 * @license   Apache-2.0
 */
interface Container extends \Psr\Container\ContainerInterface
{
    /**
     * Whether this container or its parent contains a component with the given name.
     *
     * (An alias for {@link has()}, left for backward compatibility.)
     *
     * @param string $name The component name
     * @return bool
     */
    public function contains(string $name): bool;

    /**
     * Whether this container or its parent contains a component with the given type.
     *
     * @param string $type The name of a class or one of PHP's language types
     *     (i.e. bool, int, float, string, array, resource)
     * @return bool
     */
    public function containsType(string $type): bool;

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * If this container doesn't have a value for that name, it will delegate to
     * its parent.
     *
     * @param string $id Identifier of the entry to look for.
     * @return mixed The component
     * @throws \Caridea\Container\Exception\Missing  No entry was found for
     *     **this** identifier in this container or its parents.
     */
    public function get($id);

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
    public function getByType(string $type): array;

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
    public function getFirst(string $type);

    /**
     * Gets all registered component names (excluding any in the parent container).
     *
     * @return array of strings
     */
    public function getNames(): array;

    /**
     * Gets the parent container.
     *
     * @return Container|null
     */
    public function getParent(): ?Container;

    /**
     * Gets the type of component with the given name.
     *
     * If this container doesn't have a value for that name, it will delegate to
     * its parent.
     *
     * @param string $name The component name
     * @return string|null The component type, either a class name or one of PHP's language types
     *     (i.e. bool, int, float, string, array, resource)
     */
    public function getType(string $name): ?string;

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
    public function named(string $name, string $type);
}
