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

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Fqsen;
use PHPUnit\Framework\TestCase;

/**
 * @uses \phpDocumentor\Reflection\Php\Trait_
 * @uses \phpDocumentor\Reflection\Php\Interface_
 * @uses \phpDocumentor\Reflection\Php\Class_
 * @uses \phpDocumentor\Reflection\Php\Namespace_
 * @uses \phpDocumentor\Reflection\Php\Function_
 * @uses \phpDocumentor\Reflection\Php\Constant
 *
 * @coversDefaultClass \phpDocumentor\Reflection\Php\File
 * @covers ::__construct
 * @covers ::<private>
 */
final class FileTest extends TestCase
{
    public const EXAMPLE_HASH = 'a-hash-string';

    public const EXAMPLE_NAME = 'a-path-string';

    public const EXAMPLE_PATH = 'example/' . self::EXAMPLE_NAME;

    public const EXAMPLE_SOURCE = 'a-source-string';

    /** @var File $fixture */
    protected $fixture;

    /** @var DocBlock */
    private $docBlock;

    /**
     * Creates a new (emoty) fixture object.
     */
    protected function setUp() : void
    {
        $this->docBlock = new DocBlock('');

        $this->fixture = new File(static::EXAMPLE_HASH, static::EXAMPLE_PATH, static::EXAMPLE_SOURCE, $this->docBlock);
    }

    /**
     * @covers ::getClasses
     * @covers ::AddClass
     */
    public function testAddAndGetClasses() : void
    {
        $this->assertEmpty($this->fixture->getClasses());

        $class = new Class_(new Fqsen('\MySpace\MyClass'));
        $this->fixture->addClass($class);

        $this->assertEquals(['\MySpace\MyClass' => $class], $this->fixture->getClasses());
    }

    /**
     * @uses \phpDocumentor\Reflection\Php\Visibility
     *
     * @covers ::getConstants
     * @covers ::addConstant
     */
    public function testAddAndGetConstants() : void
    {
        $this->assertEmpty($this->fixture->getConstants());

        $constant = new Constant(new Fqsen('\MySpace::MY_CONSTANT'));
        $this->fixture->addConstant($constant);

        $this->assertEquals(['\MySpace::MY_CONSTANT' => $constant], $this->fixture->getConstants());
    }

    /**
     * @covers ::getFunctions
     * @covers ::addFunction
     */
    public function testAddAndGetFunctions() : void
    {
        $this->assertEmpty($this->fixture->getFunctions());

        $function = new Function_(new Fqsen('\MySpace::MyFunction()'));
        $this->fixture->addFunction($function);

        $this->assertEquals(['\MySpace::MyFunction()' => $function], $this->fixture->getFunctions());
    }

    /**
     * @covers ::getInterfaces
     * @covers ::addInterface
     */
    public function testAddAndGetInterfaces() : void
    {
        $this->assertEmpty($this->fixture->getInterfaces());

        $interface = new Interface_(new Fqsen('\MySpace\MyInterface'), []);
        $this->fixture->addInterface($interface);

        $this->assertEquals(['\MySpace\MyInterface' => $interface], $this->fixture->getInterfaces());
    }

    /**
     * @covers ::getTraits
     * @covers ::addTrait
     */
    public function testAddAndGetTraits() : void
    {
        $this->assertEmpty($this->fixture->getTraits());

        $trait = new Trait_(new Fqsen('\MySpace\MyTrait'));
        $this->fixture->addTrait($trait);

        $this->assertEquals(['\MySpace\MyTrait' => $trait], $this->fixture->getTraits());
    }

    /**
     * @covers ::getDocBlock
     */
    public function testGetDocBlock() : void
    {
        $this->assertSame($this->docBlock, $this->fixture->getDocBlock());
    }

    /**
     * @covers ::getHash
     */
    public function testGetHash() : void
    {
        $this->assertSame(self::EXAMPLE_HASH, $this->fixture->getHash());
    }

    /**
     * @covers ::getName
     */
    public function testGetName() : void
    {
        $this->assertSame(self::EXAMPLE_NAME, $this->fixture->getName());
    }

    /**
     * @covers ::getPath
     */
    public function testSetAndGetPath() : void
    {
        $this->assertSame(self::EXAMPLE_PATH, $this->fixture->getPath());
    }

    /**
     * @covers ::getSource
     */
    public function testSetAndGetSource() : void
    {
        $this->assertSame(self::EXAMPLE_SOURCE, $this->fixture->getSource());
    }

    /**
     * @covers ::addNamespace
     * @covers ::getNamespaces
     */
    public function testSetAndGetNamespaceAliases() : void
    {
        $this->assertEmpty($this->fixture->getNamespaces());

        $this->fixture->addNamespace(new Fqsen('\MyNamepace\Foo'));

        $this->assertEquals(['\MyNamepace\Foo' => new Fqsen('\MyNamepace\Foo')], $this->fixture->getNamespaces());
    }

    /**
     * @covers ::getIncludes
     * @covers ::addInclude
     */
    public function testAddAndGetIncludes() : void
    {
        $this->assertEmpty($this->fixture->getIncludes());

        $include = static::EXAMPLE_PATH;
        $this->fixture->addInclude($include);

        $this->assertSame([static::EXAMPLE_PATH => static::EXAMPLE_PATH], $this->fixture->getIncludes());
    }
}
