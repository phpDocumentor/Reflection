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

namespace phpDocumentor\Reflection\Php;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Fqsen;

/**
 * Tests the functionality for the Constant class.
 * @coversDefaultClass phpDocumentor\Reflection\Php\Constant
 */
class ConstantTest extends \PHPUnit_Framework_TestCase
{
    /** @var Constant $fixture */
    protected $fixture;

    /**
     * @var Fqsen
     */
    private $fqsen;

    /**
     * @var DocBlock
     */
    private $docBlock;

    /**
     * @var string
     */
    private $value = 'Value';

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp()
    {
        $this->fqsen = new Fqsen('\MySpace\CONSTANT');
        $this->docBlock = new DocBlock('');
        $this->fixture = new Constant($this->fqsen, $this->docBlock, $this->value);
    }

    /**
     * @covers ::getValue
     * @covers ::__construct
     */
    public function testGetValue()
    {
        $this->assertSame($this->value, $this->fixture->getValue());
    }

    /**
     * @covers ::__construct
     * @covers ::getFqsen
     * @covers ::getName
     */
    public function testGetFqsen()
    {
        $this->assertSame($this->fqsen, $this->fixture->getFqsen());
        $this->assertSame($this->fqsen->getName(), $this->fixture->getName());
    }

    /**
     * @covers ::__construct
     * @covers ::getDocBlock
     */
    public function testGetDocblock()
    {
        $this->assertSame($this->docBlock, $this->fixture->getDocBlock());
    }

}
