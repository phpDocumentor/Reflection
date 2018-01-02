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

namespace phpDocumentor\Reflection\Php;

use Mockery as m;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Fqsen;
use PHPUnit\Framework\TestCase;

/**
 * Tests the functionality for the Interface_ class.
 * @coversDefaultClass phpDocumentor\Reflection\Php\Interface_
 */
// @codingStandardsIgnoreStart
class Interface_Test extends TestCase
// @codingStandardsIgnoreEnd
{
    /** @var Interface_ $fixture */
    private $fixture;

    /**
     * @var Fqsen
     */
    private $fqsen;

    /**
     * @var DocBlock
     */
    private $docBlock;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp()
    {
        $this->fqsen = new Fqsen('\MySpace\MyInterface');
        $this->docBlock = new DocBlock('');
        $this->fixture = new Interface_($this->fqsen, array(), $this->docBlock);
    }

    protected function tearDown()
    {
        m::close();
    }

    /**
     * @covers ::__construct
     * @covers ::getFqsen
     */
    public function testGetFqsen()
    {
        $this->assertSame($this->fqsen, $this->fixture->getFqsen());
    }

    /**
     * @covers ::__construct
     * @covers ::getDocBlock
     */
    public function testGetDocblock()
    {
        $this->assertSame($this->docBlock, $this->fixture->getDocBlock());
    }

    /**
     * @covers ::addConstant
     * @covers ::getConstants
     */
    public function testSettingAndGettingConstants()
    {
        $this->assertEquals(array(), $this->fixture->getConstants());

        $constant = new Constant(new Fqsen('\MySpace\MyInterface::MY_CONSTANT'));

        $this->fixture->addConstant($constant);

        $this->assertEquals(array('\MySpace\MyInterface::MY_CONSTANT' => $constant), $this->fixture->getConstants());
    }

    /**
     * @covers ::addMethod
     * @covers ::getMethods
     */
    public function testSettingAndGettingMethods()
    {
        $this->assertEquals(array(), $this->fixture->getMethods());

        $method = new Method(new Fqsen('\MySpace\MyInterface::myMethod()'));

        $this->fixture->addMethod($method);

        $this->assertEquals(array('\MySpace\MyInterface::myMethod()' => $method), $this->fixture->getMethods());
    }
}
