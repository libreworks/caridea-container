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
 * A concrete, immutable dependency injection container with event publishing capabilities.
 *
 * @copyright 2015-2016 LibreWorks contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache 2.0 License
 */
class Objects extends AbstractContainer implements \Caridea\Event\Publisher
{
    /**
     * @var \Caridea\Container\Provider[] Associative array of string names to providers
     */
    protected $providers = [];
    /**
     * @var \SplObjectStorage A collection of event listeners
     */
    protected $listeners;
    
    /**
     * Creates a new Object container.
     *
     * You might find it easier to use the `Builder` instead of this
     * constructor. Compare this first example…
     *
     * ```php
     * $props = new \Caridea\Container\Properties([
     *     'mail.host' => 'mail.example.net'
     * ]);
     * $objects = new \Caridea\Container\Objects([
     *     'mailService' => new Provider('My\Mail\Service', function($c){
     *         return new \My\Mail\Service($c['mail.host']);
     *     }, true),
     *     'userService' => new Provider('My\User\Service', function($c){
     *         return new \My\User\Service($c['mailService']);
     *     }, false)
     * ], $props);
     * ```
     *
     * …to this one:
     *
     * ```php
     * $props = new \Caridea\Container\Properties([
     *     'mail.host' => 'mail.example.net'
     * ]);
     * $objects = \Caridea\Container\Objects::builder()
     *     ->lazy('mailService', 'My\Mail\Service', function($c){
     *         return new \My\Mail\Service($c['mail.host']);
     *     })
     *     ->proto('userService', 'My\User\Service', function($c){
     *         return new \My\User\Service($c['mailService']);
     *     })
     *     ->build($props);
     * ```
     *
     * @param \Caridea\Container\Provider[] $providers with names as keys
     * @param \Caridea\Container\Container $parent An optional parent container
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
    
    /**
     * Creates a new Builder.
     *
     * @return \Caridea\Container\Builder A new `Objects` builder
     */
    public static function builder()
    {
        return new Builder();
    }
    
    /**
     * Retrieves the value
     *
     * @param string $name The value name
     */
    protected function doGet($name)
    {
        $value = $this->providers[$name]->get($this);
        $type = $this->types[$name];
        if (!($value instanceof $type)) {
            throw new \UnexpectedValueException("The value that came from the "
                . "provider was supposed to be a $type, but it returned a "
                . (is_object($value) ? get_class($value) : gettype($value)));
        }
        if ($value instanceof \Caridea\Event\Listener) {
            // SplObjectStorage is a set; it will only ever contain uniques
            $this->listeners->attach($value);
        }
        return $value;
    }
    
    /**
     * Queues an event to be sent to Listeners.
     *
     * @param \Caridea\Event\Event $event The event to publish
     */
    public function publish(\Caridea\Event\Event $event)
    {
        foreach ($this->listeners as $listener) {
            $listener->notify($event);
        }
    }
}
