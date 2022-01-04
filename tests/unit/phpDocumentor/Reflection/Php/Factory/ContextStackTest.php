<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php\Factory;

use OutOfBoundsException;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Class_ as ClassElement;
use phpDocumentor\Reflection\Php\Method;
use phpDocumentor\Reflection\Php\Project;
use phpDocumentor\Reflection\Types\Context;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Factory\ContextStack
 */
final class ContextStackTest extends PHPUnitTestCase
{
    /**
     * @covers ::__construct
     * @covers ::getTypeContext
     * @covers ::getProject
     */
    public function testCreate(): void
    {
        $project = new Project('myProject');
        $typeContext = new Context('myNamespace');
        $context = new ContextStack($project, $typeContext);

        self::assertSame($project, $context->getProject());
        self::assertSame($typeContext, $context->getTypeContext());
    }

    /**
     * @covers ::__construct
     * @covers ::peek
     */
    public function testPeekThowsWhenEmpty(): void
    {
        $this->expectException(OutOfBoundsException::class);
        $project = new Project('myProject');
        $typeContext = new Context('myNamespace');
        $context = new ContextStack($project, $typeContext);

        $context->peek();
    }

    /**
     * @covers ::__construct
     * @covers ::peek
     * @covers ::push
     * @covers ::getTypeContext
     * @covers ::getProject
     * @covers ::createFromSelf
     */
    public function testPeekReturnsTopOfStack(): void
    {
        $class = new ClassElement(new Fqsen('\MyClass'));

        $project = new Project('myProject');
        $typeContext = new Context('myNamespace');
        $context = new ContextStack($project, $typeContext);
        $context = $context->push($class);

        self::assertSame($class, $context->peek());
        self::assertSame($project, $context->getProject());
        self::assertSame($typeContext, $context->getTypeContext());
    }

    /**
     * @covers ::__construct
     * @covers ::withTypeContext
     * @covers ::peek
     * @covers ::push
     * @covers ::getTypeContext
     * @covers ::getProject
     * @covers ::createFromSelf
     */
    public function testCreateWithTypeContext(): void
    {
        $class = new ClassElement(new Fqsen('\MyClass'));

        $project = new Project('myProject');
        $typeContext = new Context('myNamespace');
        $context = new ContextStack($project);
        $context = $context->push($class)->withTypeContext($typeContext);

        self::assertSame($class, $context->peek());
        self::assertSame($project, $context->getProject());
        self::assertSame($typeContext, $context->getTypeContext());
    }

    /**
     * @covers ::__construct
     * @covers ::search
     */
    public function testSearchEmptyStackResultsInNull(): void
    {
        $project = new Project('myProject');
        $context = new ContextStack($project);

        self::assertNull($context->search(ClassElement::class));
    }

    /**
     * @covers ::__construct
     * @covers ::search
     */
    public function testSearchStackForExistingElementTypeWillReturnTheFirstHit(): void
    {
        $class = new ClassElement(new Fqsen('\MyClass'));
        $project = new Project('myProject');
        $context = new ContextStack($project);
        $context = $context
            ->push(new ClassElement(new Fqsen('\OtherClass')))
            ->push($class)
            ->push(new Method(new Fqsen('\MyClass::method()')));

        self::assertSame($class, $context->search(ClassElement::class));
    }
}
