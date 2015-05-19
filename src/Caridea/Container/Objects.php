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
 * A concrete dependency injection container with event publishing capabilities.
 * 
 * @copyright 2015 LibreWorks contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache 2.0 License
 */
class Objects extends AbstractContainer implements \Caridea\Event\Publisher
{
    /**
     * @var \Caridea\Container\Provider[] with string keys
     */
    protected $providers = [];
    /**
     * @var \SplObjectStorage
     */
    protected $listeners;
    
    /**
     * Creates a new Object container.
     * 
     * You might find it easier to use the {@link Builder} instead of this
     * constructor.
     * 
     * ```
     * use \Caridea\Reflect\Type;
     * $props = new \Caridea\Container\Properties([
     *     'mail.host' => 'mail.example.net'
     * ]);
     * $objects = new \Caridea\Container\Objects([
     *     'mailService' => new Provider('My\Mail\Service', function($c){
     *         return new \My\Mail\Service($c['mail.host']);
     *     }),
     *     'userService' => new Provider('My\User\Service', function($c){
     *         return new \My\User\Service($c['mailService']);
     *     })
     * ], $props);
     * ```
     * 
     * @param \Caridea\Container\Provider[] $providers with names as keys
     * @param \Caridea\Container\Container $parent
     */
    public function __construct(array $providers, Container $parent = null)
    {
        $types = [];
        foreach ($providers as $k => $v) {
            if ($v instanceof Provider) {
                $this->providers[(string)$k] = $v;
                $types[(string)$k] = $v->getType();
            }
        }
        parent::__construct($types, $parent);
        $this->listeners = new \SplObjectStorage();
    }
    
    protected function doGet($name)
    {
        $value = $this->providers[$name]->get($this);
        if ($value instanceof \Caridea\Event\Listener) {
            // SplObjectStorage is a set; it will only ever contain uniques
            $this->listeners->attach($value);
        }
        return $value;
    }
    
    public function publish(\Caridea\Event\Event $event)
    {
        foreach ($this->listeners as $listener) {
            $listener->notify($event);
        }
    }
}