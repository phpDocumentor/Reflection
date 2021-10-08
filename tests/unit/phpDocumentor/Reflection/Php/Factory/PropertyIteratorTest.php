<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php\Factory;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use PhpParser\Comment\Doc;
use PhpParser\Node\Stmt\Property as PropertyNode;
use PhpParser\Node\Stmt\PropertyProperty;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Factory\PropertyIterator
 * @covers ::__construct
 * @covers ::<private>
 */
class PropertyIteratorTest extends MockeryTestCase
{
    /**
     * @covers ::current()
     * @covers ::next()
     * @covers ::valid()
     * @covers ::rewind()
     * @covers ::getName()
     */
    public function testIterateProps(): void
    {
        $prop1 = new PropertyProperty('prop1');
        $prop2 = new PropertyProperty('prop2');

        $propertyNode = new PropertyNode(1, [$prop1, $prop2]);

        $i = 1;
        foreach (new PropertyIterator($propertyNode) as $property) {
            $this->assertEquals('prop' . $i, $property->getName());
            ++$i;
        }
    }

    /**
     * @covers ::key()
     * @covers ::next()
     */
    public function testKey(): void
    {
        $propertyMock = m::mock(PropertyNode::class);

        $fixture = new PropertyIterator($propertyMock);

        $this->assertEquals(0, $fixture->key());
        $fixture->next();
        $this->assertEquals(1, $fixture->key());
    }

    /**
     * @covers ::__construct
     * @covers ::isPublic
     * @covers ::isProtected
     * @covers ::isPrivate
     * @covers ::isStatic
     * @covers ::isReadOnly
     * @covers ::getLine
     */
    public function testProxyMethods(): void
    {
        $propertyMock = m::mock(PropertyNode::class);
        $propertyMock->shouldReceive('isPublic')->once()->andReturn(true);
        $propertyMock->shouldReceive('isProtected')->once()->andReturn(true);
        $propertyMock->shouldReceive('isPrivate')->once()->andReturn(true);
        $propertyMock->shouldReceive('isStatic')->once()->andReturn(true);
        $propertyMock->shouldReceive('isReadOnly')->once()->andReturn(true);
        $propertyMock->shouldReceive('getLine')->once()->andReturn(10);

        $fixture = new PropertyIterator($propertyMock);

        $this->assertTrue($fixture->isStatic());
        $this->assertTrue($fixture->isReadOnly());
        $this->assertTrue($fixture->isPrivate());
        $this->assertTrue($fixture->isProtected());
        $this->assertTrue($fixture->isPublic());
        $this->assertEquals(10, $fixture->getLine());
    }

    /**
     * @covers ::__construct
     * @covers ::getDefault
     */
    public function testGetDefault(): void
    {
        $prop = m::mock(PropertyProperty::class);
        $prop->default = 'myDefault';
        $property = new PropertyNode(1, [$prop]);

        $fixture = new PropertyIterator($property);

        $this->assertEquals('myDefault', $fixture->getDefault());
    }

    /**
     * @covers ::getDocComment
     */
    public function testGetDocCommentPropFirst(): void
    {
        $prop = m::mock(PropertyProperty::class);
        $propertyNode = m::mock(PropertyNode::class);
        $propertyNode->props = [$prop];

        $prop->shouldReceive('getDocComment')->once()->andReturn(new Doc('test'));
        $propertyNode->shouldReceive('getDocComment')->never();

        $fixture = new PropertyIterator($propertyNode);

        $this->assertEquals('test', $fixture->getDocComment()->getText());
    }

    /**
     * @covers ::getDocComment
     */
    public function testGetDocComment(): void
    {
        $prop = m::mock(PropertyProperty::class);
        $propertyNode = m::mock(PropertyNode::class);
        $propertyNode->props = [$prop];

        $prop->shouldReceive('getDocComment')->once()->andReturnNull();
        $propertyNode->shouldReceive('getDocComment')->once()->andReturn(new Doc('test'));

        $fixture = new PropertyIterator($propertyNode);

        $this->assertEquals('test', $fixture->getDocComment()->getText());
    }
}
