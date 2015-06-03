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
 * Generated by PHPUnit_SkeletonGenerator on 2015-05-18 at 15:28:53.
 */
class ObjectsTest extends \PHPUnit_Framework_TestCase implements \Caridea\Event\Listener
{
    /**
     * @covers Caridea\Container\Objects::__construct
     * @covers Caridea\Container\AbstractContainer::__construct
     * @covers Caridea\Container\Objects::doGet
     */
    public function testBasic()
    {
        $providers = [
            'not.a.provider' => 123,
            'myQueue' => new Provider('SplQueue', function($c){
                return new \SplQueue();
            }),
            'secondArray' => new Provider('ArrayObject', function($c){
                return new \ArrayObject([4, 5, 6]);
            }),
            'myArray' => new Provider('ArrayObject', function($c){
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
            'myListener' => new Provider(__CLASS__, function($c) use($self){
                return $self;
            })
        ];
        $object = new Objects($providers);
        $object->get('myListener');
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
            'myQueue' => new Provider('SplQueue', function($c){
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
            'myQueue' => new Provider('SplQueue', function($c){
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
}