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
 * @copyright 2015-2018 LibreWorks contributors
 * @license   Apache-2.0
 */
namespace Caridea\Container;

/**
 * Allows objects to be made aware of the container that created them.
 */
trait ContainerSetter
{
    /**
     * @var \Caridea\Container\Container
     */
    protected $container;

    /**
     * Sets the container.
     *
     * @param \Caridea\Container\Container $container The container to set
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }
}