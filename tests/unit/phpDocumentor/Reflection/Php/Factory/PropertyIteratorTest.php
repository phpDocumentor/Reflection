<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2018 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */


namespace phpDocumentor\Reflection\Php\Factory;

use Mockery as m;
use PhpParser\Node\Stmt\Property as PropertyNode;
use PhpParser\Node\Stmt\PropertyProperty;
use PHPUnit\Framework\TestCase;

/**
 * Class PropertyIteratorTest
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Factory\PropertyIterator
 */
class PropertyIteratorTest extends TestCase
{

    protected function tearDown()
    {
        m::close();
    }

    /**
     * @covers ::current()
     * @covers ::next()
     * @covers ::valid()
     * @covers ::rewind()
     * @covers ::getName()
     */
    public function testIterateProps()
    {
        $prop1 = new PropertyProperty('prop1');
        $prop2 = new PropertyProperty('prop2');

        $propertyNode = new PropertyNode(1, [$prop1, $prop2]);

        $i = 1;
        foreach (new PropertyIterator($propertyNode) as $property) {
            $this->assertEquals('prop' . $i, $property->getName());
            $i++;
        }
    }

    /**
     * @covers ::key()
     * @covers ::next()
     */
    public function testKey()
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
     * @covers ::getLine
     */
    public function testProxyMethods()
    {
        $propertyMock = m::mock(PropertyNode::class);
        $propertyMock->shouldReceive('isPublic')->once()->andReturn(true);
        $propertyMock->shouldReceive('isProtected')->once()->andReturn(true);
        $propertyMock->shouldReceive('isPrivate')->once()->andReturn(true);
        $propertyMock->shouldReceive('isStatic')->once()->andReturn(true);
        $propertyMock->shouldReceive('getLine')->once()->andReturn(10);

        $fixture = new PropertyIterator($propertyMock);

        $this->assertTrue($fixture->isStatic());
        $this->assertTrue($fixture->isPrivate());
        $this->assertTrue($fixture->isProtected());
        $this->assertTrue($fixture->isPublic());
        $this->assertEquals(10, $fixture->getLine());
    }

    /**
     * @covers ::__construct
     * @covers ::getDefault
     */
    public function testGetDefault()
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
    public function testGetDocCommentPropFirst()
    {
        $prop = m::mock(PropertyProperty::class);
        $propertyNode = m::mock(PropertyNode::class);
        $propertyNode->props = [$prop];

        $prop->shouldReceive('getDocComment')->once()->andReturn('test');
        $propertyNode->shouldReceive('getDocComment')->never();

        $fixture = new PropertyIterator($propertyNode);

        $this->assertEquals('test', $fixture->getDocComment());
    }

    /**
     * @covers ::getDocComment
     */
    public function testGetDocComment()
    {
        $prop = m::mock(PropertyProperty::class);
        $propertyNode = m::mock(PropertyNode::class);
        $propertyNode->props = [$prop];

        $prop->shouldReceive('getDocComment')->once()->andReturnNull();
        $propertyNode->shouldReceive('getDocComment')->once()->andReturn('test');

        $fixture = new PropertyIterator($propertyNode);

        $this->assertEquals('test', $fixture->getDocComment());
    }
}
