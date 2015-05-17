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
 * Dependency injection container.
 * 
 * @copyright 2015 LibreWorks contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache 2.0 License
 */
interface Container
{
    /**
     * Whether this container contains a component with the given name.
     *
     * @param string $name The component name
     * @return boolean
     */
    function contains($name);

    /**
     * Whether this container contains a component with the given type.
     * 
     * @param \Caridea\Reflect\Type|string $type A Type or the name of a class
     * @return boolean
     */
    function containsType($type);

    /**
     * Gets the component by name.
     * 
     * @param string $name The component name
     * @return object The component or null if the name isn't registered
     */
    function get($name);

    /**
     * Gets the components in the contanier for the given type.
     *
     * @param \Caridea\Reflect\Type|string $type A Type or string class name
     * @return array keys are component names, values are components themselves
     */
    function getByType($type);

    /**
     * Gets all registered component names.
     *
     * @return array of strings
     */
    function getNames();

    /**
     * Gets the parent container.
     * 
     * @return Container
     */
    function getParent();

    /**
     * Gets the type of component with the given name.
     * 
     * @param string $name The component name
     * @return \Caridea\Reflect\Type The component type
     */
    function getType($name);
}