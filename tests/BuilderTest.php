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
 * Generated by PHPUnit_SkeletonGenerator on 2015-05-28 at 10:48:32.
 */
class BuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Builder
     */
    protected $object;
    /**
     * @var Properties
     */
    protected $parent;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Builder;
        $this->parent = new Properties(['foo' => 'bar']);
    }

    /**
     * @covers Caridea\Container\Builder::addProvider
     * @covers Caridea\Container\Builder::build
     */
    public function testAddProvider()
    {
        $this->assertSame(
            $this->object,
            $this->object->addProvider('foobar', new Provider('SplObjectStorage', function () {
                return new \SplObjectStorage();

            }, false))
        );
        $container = $this->object->build($this->parent);
        $this->assertInstanceOf(Objects::class, $container);
        $this->assertSame($this->parent, $container->getParent());
        $this->assertInstanceOf('SplObjectStorage', $container->get('foobar'));
    }

    /**
     * @covers Caridea\Container\Builder::eager
     * @covers Caridea\Container\Builder::build
     */
    public function testEager()
    {
        $called = false;
        $this->assertSame($this->object, $this->object->eager('foobar', 'ArrayObject', function () use (&$called) {
            $called = true;
            return new \ArrayObject();
        }));
        $container = $this->object->build($this->parent);
        $this->assertInstanceOf(Objects::class, $container);
        $this->assertSame($this->parent, $container->getParent());
        $this->assertTrue($called);
        $component = $container->get('foobar');
        $this->assertInstanceOf('ArrayObject', $component);
        $this->assertSame($component, $container->get('foobar'));
    }

    /**
     * @covers Caridea\Container\Builder::lazy
     * @covers Caridea\Container\Builder::build
     */
    public function testLazy()
    {
        $called = false;
        $this->assertSame($this->object, $this->object->lazy('foobar', 'ArrayObject', function () use (&$called) {
            $called = true;
            return new \ArrayObject();
        }));
        $container = $this->object->build($this->parent);
        $this->assertInstanceOf(Objects::class, $container);
        $this->assertSame($this->parent, $container->getParent());
        $this->assertFalse($called);
        $component = $container->get('foobar');
        $this->assertTrue($called);
        $this->assertInstanceOf('ArrayObject', $component);
        $this->assertSame($component, $container->get('foobar'));
    }

    /**
     * @covers Caridea\Container\Builder::proto
     * @covers Caridea\Container\Builder::build
     */
    public function testProto()
    {
        $called = false;
        $this->assertSame($this->object, $this->object->proto('foobar', 'ArrayObject', function () use (&$called) {
            $called = true;
            return new \ArrayObject();
        }));
        $container = $this->object->build($this->parent);
        $this->assertInstanceOf(Objects::class, $container);
        $this->assertSame($this->parent, $container->getParent());
        $this->assertFalse($called);
        $component = $container->get('foobar');
        $this->assertTrue($called);
        $this->assertInstanceOf('ArrayObject', $component);
        $this->assertNotSame($component, $container->get('foobar'));
    }
}
