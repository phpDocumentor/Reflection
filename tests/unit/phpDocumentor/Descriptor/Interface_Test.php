<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

use Mockery as m;
use phpDocumentor\Descriptor\Tag\AuthorDescriptor;
use phpDocumentor\Descriptor\Tag\VersionDescriptor;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Fqsen;
use PhpParser\Comment\Doc;

/**
 * Tests the functionality for the Interface_ class.
 * @coversDefaultClass phpDocumentor\Descriptor\Interface_
 */
class Interface_Test extends \PHPUnit_Framework_TestCase
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
        $this->fixture = new Interface_($this->fqsen, $this->docBlock);
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

        $constant = new ConstantDescriptor();
        $constant->setFullyQualifiedStructuralElementName('\MySpace\MyInterface::MY_CONSTANT');

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
