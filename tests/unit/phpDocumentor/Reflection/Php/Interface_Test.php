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

namespace phpDocumentor\Reflection\Php;

use Mockery as m;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Fqsen;
use PHPUnit\Framework\TestCase;

/**
 * Tests the functionality for the Interface_ class.
 *
 * @coversDefaultClass phpDocumentor\Reflection\Php\Interface_
 */
// @codingStandardsIgnoreStart
class Interface_Test extends TestCase
// @codingStandardsIgnoreEnd
{
    /** @var Interface_ $fixture */
    private $fixture;

    /** @var Fqsen */
    private $fqsen;

    /** @var DocBlock */
    private $docBlock;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp() : void
    {
        $this->fqsen    = new Fqsen('\MySpace\MyInterface');
        $this->docBlock = new DocBlock('');
        $this->fixture  = new Interface_($this->fqsen, [], $this->docBlock);
    }

    protected function tearDown() : void
    {
        m::close();
    }

    /**
     * @covers ::__construct
     * @covers ::getFqsen
     */
    public function testGetFqsen() : void
    {
        $this->assertSame($this->fqsen, $this->fixture->getFqsen());
    }

    /**
     * @covers ::__construct
     * @covers ::getDocBlock
     */
    public function testGetDocblock() : void
    {
        $this->assertSame($this->docBlock, $this->fixture->getDocBlock());
    }

    /**
     * @covers ::addConstant
     * @covers ::getConstants
     */
    public function testSettingAndGettingConstants() : void
    {
        $this->assertEquals([], $this->fixture->getConstants());

        $constant = new Constant(new Fqsen('\MySpace\MyInterface::MY_CONSTANT'));

        $this->fixture->addConstant($constant);

        $this->assertEquals(['\MySpace\MyInterface::MY_CONSTANT' => $constant], $this->fixture->getConstants());
    }

    /**
     * @covers ::addMethod
     * @covers ::getMethods
     */
    public function testSettingAndGettingMethods() : void
    {
        $this->assertEquals([], $this->fixture->getMethods());

        $method = new Method(new Fqsen('\MySpace\MyInterface::myMethod()'));

        $this->fixture->addMethod($method);

        $this->assertEquals(['\MySpace\MyInterface::myMethod()' => $method], $this->fixture->getMethods());
    }
}
