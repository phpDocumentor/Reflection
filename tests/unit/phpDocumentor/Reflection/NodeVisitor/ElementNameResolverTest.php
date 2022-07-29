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

namespace phpDocumentor\Reflection\NodeVisitor;

use PhpParser\Node\Const_;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Enum_;
use PhpParser\Node\Stmt\EnumCase;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\PropertyProperty;
use PhpParser\NodeTraverser;
use PHPUnit\Framework\TestCase;

/**
 * @uses \phpDocumentor\Reflection\NodeVisitor\ElementNameResolver::beforeTraverse
 *
 * @coversDefaultClass \phpDocumentor\Reflection\NodeVisitor\ElementNameResolver
 * @covers ::<private>
 */
class ElementNameResolverTest extends TestCase
{
    private ElementNameResolver $fixture;

    protected function setUp(): void
    {
        $this->fixture = new ElementNameResolver();
        $this->fixture->beforeTraverse([]);
    }

    /**
     * @covers ::enterNode
     */
    public function testFunctionWithoutNamespace(): void
    {
        $function = new Function_('myFunction');
        $this->fixture->enterNode($function);

        $this->assertEquals('\myFunction()', (string) $function->fqsen);
    }

    /**
     * @covers ::enterNode
     */
    public function testWithClass(): void
    {
        $class = new Class_('myClass');
        $this->fixture->enterNode($class);

        $this->assertEquals('\myClass', (string) $class->fqsen);
    }

    /**
     * @covers ::enterNode
     */
    public function testWithClassMethod(): void
    {
        $class = new Class_('myClass');
        $this->fixture->enterNode($class);

        $method = new ClassMethod('method');
        $this->fixture->enterNode($method);

        $this->assertEquals('\myClass::method()', (string) $method->fqsen);
    }

    /**
     * @covers ::enterNode
     */
    public function testWithClassProperty(): void
    {
        $class = new Class_('myClass');
        $this->fixture->enterNode($class);

        $method = new PropertyProperty('name');
        $this->fixture->enterNode($method);

        $this->assertEquals('\myClass::$name', (string) $method->fqsen);
    }

    /**
     * If anonymous classes were processed, we would obtain a
     * InvalidArgumentException for an invalid Fqsen.
     *
     * @covers ::enterNode
     */
    public function testDoesNotEnterAnonymousClass(): void
    {
        $class = new Class_(null);
        $this->assertEquals(
            NodeTraverser::DONT_TRAVERSE_CHILDREN,
            $this->fixture->enterNode($class)
        );
    }

    /**
     * @link https://github.com/phpDocumentor/Reflection/issues/103
     *
     * @covers ::enterNode
     * @covers ::leaveNode
     */
    public function testAnonymousClassDoesNotPopParts(): void
    {
        $anonymousClass = new Class_(null);

        $new = new New_($anonymousClass);

        $namespace = new Namespace_(new Name('ANamespace'), $new);

        $this->fixture->enterNode($namespace);
        $this->fixture->enterNode($new);
        $this->fixture->enterNode($anonymousClass);
        $this->fixture->leaveNode($anonymousClass);
        $this->fixture->leaveNode($new);
        $this->fixture->leaveNode($namespace);

        $this->assertTrue(true);
    }

    /**
     * @covers ::enterNode
     */
    public function testClassConstant(): void
    {
        $const      = new Const_('MY_CLASS', new String_('value'));
        $classConst = new ClassConst([$const]);
        $class      = new Class_('myClass');

        $this->fixture->enterNode($class);
        $this->fixture->enterNode($classConst);
        $this->fixture->enterNode($const);

        $this->assertEquals('\\myClass::MY_CLASS', (string) $const->fqsen);
    }

    /**
     * @covers ::enterNode
     */
    public function testNamespacedConstant(): void
    {
        $const     = new Const_('MY_CLASS', new String_('value'));
        $namespace = new Namespace_(new Name('name'));

        $this->fixture->enterNode($namespace);
        $this->fixture->enterNode($const);

        $this->assertEquals('\\name\\MY_CLASS', (string) $const->fqsen);
    }

    /**
     * @covers ::enterNode
     */
    public function testNoNameNamespace(): void
    {
        $const     = new Const_('MY_CLASS', new String_('value'));
        $namespace = new Namespace_(null);

        $this->fixture->enterNode($namespace);
        $this->fixture->enterNode($const);

        $this->assertEquals('\\MY_CLASS', (string) $const->fqsen);
    }

    /**
     * @covers ::enterNode
     */
    public function testWithEnumWithCase(): void
    {
        $enum = new Enum_('myEnum');
        $this->fixture->enterNode($enum);

        $case = new EnumCase('VALUE1');
        $this->fixture->enterNode($case);

        $this->assertEquals('\myEnum::VALUE1', (string) $case->fqsen);
    }
}
