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
 * Dependency injection container that only has runtime configuration.
 *
 * This container is meant to be used as a parent to a container which has
 * real objects in it. Since this one is intended to hold configuration, you
 * should really only add scalar types and arrays. However, it's certainly
 * possible to add object instances.
 *
 * @copyright 2015 LibreWorks contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache 2.0 License
 */
class Properties extends AbstractContainer
{
    protected $values = [];
    
    /**
     * Creates a container with static configuration properties.
     *
     * Null values are silently ignored.
     *
     * ```
     * $props = new \Caridea\Container\Properties([
     *     'db.host'      => 'example.com',
     *     'db.port'      => 1337,
     *     'db.user'      => 'dba',
     *     'cache.holder' => new \SplObjectStorage(),
     *     'dates.nye'    => new \DateTime('2015-12-31')
     * ]);
     * ```
     *
     * @param array $properties String keys, mixed values.
     * @param \Caridea\Container\Container $parent An optional parent container.
     */
    public function __construct(array $properties, Container $parent = null)
    {
        $types = [];
        foreach ($properties as $k => $v) {
            if ($v !== null) {
                $types[(string)$k] = self::typeof($v);
                $this->values[(string)$k] = $v;
            }
        }
        parent::__construct($types, $parent);
    }
    
    /**
     * More predictable results than `gettype`.
     *
     * @param mixed $v The value to evaluate
     */
    protected static function typeof($v)
    {
        if (is_bool($v)) {
            return 'bool';
        } elseif (is_int($v)) {
            return 'int';
        } elseif (is_float($v)) {
            return 'float';
        } elseif (is_string($v)) {
            return 'string';
        } elseif (is_array($v)) {
            return 'array';
        } elseif (is_resource($v)) {
            return 'resource';
        }
        return get_class($v);
    }
    
    protected function doGet($name)
    {
        return isset($this->values[$name]) ? $this->values[$name] : null;
    }
}
