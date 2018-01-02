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
 * Generated by PHPUnit_SkeletonGenerator on 2015-05-18 at 15:28:53.
 */
class ObjectsTest extends \PHPUnit\Framework\TestCase implements \Caridea\Event\Listener, \Caridea\Event\PublisherAware, ContainerAware
{
    use \Caridea\Event\PublisherSetter;
    use ContainerSetter;

    /**
     * @covers Caridea\Container\Objects::__construct
     * @covers Caridea\Container\AbstractContainer::__construct
     * @covers Caridea\Container\Objects::doGet
     */
    public function testBasic()
    {
        $providers = [
            'not.a.provider' => 123,
            'myQueue' => new Provider('SplQueue', function ($c) {
                return new \SplQueue();
            }),
            'secondArray' => new Provider('ArrayObject', function ($c) {
                return new \ArrayObject([4, 5, 6]);
            }),
            'myArray' => new Provider('ArrayObject', function ($c) {
                return new \ArrayObject([1, 2, 3]);
            })
        ];
        $object = new Objects($providers);
        $this->assertTrue($object->contains('myArray'));
        $this->assertTrue($object->containsType('SplQueue'));
        $this->assertEquals(['secondArray' => new \ArrayObject([4, 5, 6]), 'myArray' => new \ArrayObject([1, 2, 3])], $object->getByType('ArrayObject'));
    }

    /**
     * @covers Caridea\Container\Objects::builder
     */
    public function testBuilder()
    {
        $this->assertInstanceOf(Builder::class, Objects::builder());
    }

    /**
     * @covers Caridea\Container\Objects::publish
     * @covers Caridea\Container\Objects::doGet
     */
    public function testPublish()
    {
        $self = $this;
        $providers = [
            'myListener' => new Provider(__CLASS__, function ($c) use ($self) {
                return $self;
            })
        ];
        $object = new Objects($providers);
        $this->assertNull($this->publisher);
        $this->assertNull($this->container);
        $me = $object->get('myListener');
        $this->assertSame($this, $me);
        $this->assertSame($object, $this->publisher);
        $this->assertSame($object, $this->container);
        $event = $this->getMockBuilder(\Caridea\Event\Event::class)->setConstructorArgs([$this])->getMock();
        $object->publish($event);
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage The value that came from the provider was supposed to be a SplQueue, but it returned a SplObjectStorage
     */
    public function testMismatch1()
    {
        $providers = [
            'myQueue' => new Provider('SplQueue', function ($c) {
                return new \SplObjectStorage();
            })
        ];
        $object = new Objects($providers);
        $object->get('myQueue');
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage The value that came from the provider was supposed to be a SplQueue, but it returned a string
     */
    public function testMismatch2()
    {
        $providers = [
            'myQueue' => new Provider('SplQueue', function ($c) {
                return "new \SplObjectStorage()";
            })
        ];
        $object = new Objects($providers);
        $object->get('myQueue');
    }

    public function notify(\Caridea\Event\Event $event)
    {
        $this->assertNotNull($event);
    }

    public function testNestedNotify()
    {
        $providers = [
            'one' => new Provider(ObjectsTest_Listener::class, function ($c) {
                return new ObjectsTest_Listener('One');
            }),
            'two' => new Provider(ObjectsTest_Listener::class, function ($c) {
                return new ObjectsTest_NestedListener('Two', $c);
            }),
            'three' => new Provider(ObjectsTest_Listener::class, function ($c) {
                return new ObjectsTest_Listener('Three');
            })
        ];
        $object = new Objects($providers);
        foreach ($providers as $k => $_) {
            $object->get($k);
        }
        $this->expectOutputString(implode(PHP_EOL, [
            'I am One and got a Caridea\Container\FooEvent',
            'I am Two and got a Caridea\Container\FooEvent',
            'I am One and got a Caridea\Container\NullEvent',
            'I am Two and got a Caridea\Container\NullEvent',
            'I am Three and got a Caridea\Container\NullEvent',
            'I am Three and got a Caridea\Container\FooEvent']) . PHP_EOL);
        $object->publish(new FooEvent($this));
    }
}

class FooEvent extends \Caridea\Event\Event
{
}

class NullEvent extends \Caridea\Event\Event
{
}

class ObjectsTest_Listener implements \Caridea\Event\Listener
{
    private $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function notify(\Caridea\Event\Event $event)
    {
        echo "I am {$this->name} and got a " . get_class($event), PHP_EOL;
    }
}

class ObjectsTest_NestedListener extends ObjectsTest_Listener
{
    private $publisher;

    public function __construct($name, Objects $publisher)
    {
        parent::__construct($name);
        $this->publisher = $publisher;
    }

    public function notify(\Caridea\Event\Event $msg)
    {
        parent::notify($msg);
        if ($msg instanceof FooEvent) {
            $this->publisher->publish(new NullEvent($this));
        }
    }
}
