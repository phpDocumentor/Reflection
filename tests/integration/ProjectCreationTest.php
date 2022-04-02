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

namespace phpDocumentor\Reflection;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\File\LocalFile;
use phpDocumentor\Reflection\Php\Class_;
use phpDocumentor\Reflection\Php\Function_;
use phpDocumentor\Reflection\Php\ProjectFactory;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\String_;

/**
 * Integration tests to check the correct working of processing a file into a project.
 *
 * @coversNothing
 */
class ProjectCreationTest extends MockeryTestCase
{
    /** @var ProjectFactory */
    private $fixture;

    protected function setUp() : void
    {
        $this->fixture = ProjectFactory::createInstance();
    }

    public function testCreateProjectWithFunctions() : void
    {
        $fileName = __DIR__ . '/data/simpleFunction.php';

        $project = $this->fixture->create(
            'MyProject',
            [new LocalFile($fileName)]
        );

        $this->assertArrayHasKey($fileName, $project->getFiles());
        $file = $project->getFiles()[$fileName];
        $this->assertArrayHasKey('\simpleFunction()', $file->getFunctions());

        /** @var Function_ $function */
        $function = $file->getFunctions()['\simpleFunction()'];
        $this->assertSame('\simpleFunction()', (string) $function->getFqsen());
        $this->assertCount(1, $function->getArguments());
    }

    public function testCreateProjectWithClass() : void
    {
        $fileName = __DIR__ . '/data/Pizza.php';
        $project = $this->fixture->create(
            'MyProject',
            [new LocalFile($fileName)]
        );

        $this->assertArrayHasKey($fileName, $project->getFiles());
        $this->assertArrayHasKey('\\Pizza', $project->getFiles()[$fileName]->getClasses());
        $this->assertArrayHasKey(
            '\\Pizza::PACKAGING',
            $project->getFiles()[$fileName]->getClasses()['\\Pizza']->getConstants()
        );
        $constant = $project->getFiles()[$fileName]->getClasses()['\\Pizza']->getConstants()['\\Pizza::PACKAGING'];

        $this->assertEquals('\'box\'', $constant->getValue());
    }

    public function testTypedPropertiesReturnTheirType() : void
    {
        $fileName = __DIR__ . '/data/Luigi/Pizza.php';
        $project = $this->fixture->create(
            'MyProject',
            [new LocalFile($fileName)]
        );

        /** @var Class_ $pizzaClass */
        $pizzaClass = $project->getFiles()[$fileName]->getClasses()['\\Luigi\\Pizza'];
        $this->assertArrayHasKey('\\Luigi\\Pizza::$size', $pizzaClass->getProperties());
        $this->assertEquals(new Integer(), $pizzaClass->getProperties()['\\Luigi\\Pizza::$size']->getType());
    }

    public function testFileWithDocBlock() : void
    {
        $fileName = __DIR__ . '/data/Pizza.php';
        $project = $this->fixture->create(
            'MyProject',
            [new LocalFile($fileName)]
        );

        $this->assertArrayHasKey($fileName, $project->getFiles());
        $this->assertInstanceOf(Docblock::class, $project->getFiles()[$fileName]->getDocBlock());
    }

    public function testWithNamespacedClass() : void
    {
        $fileName = __DIR__ . '/data/Luigi/Pizza.php';
        $project = $this->fixture->create(
            'MyProject',
            [new LocalFile($fileName)]
        );

        $this->assertArrayHasKey($fileName, $project->getFiles());
        $this->assertArrayHasKey('\\Luigi\\Pizza', $project->getFiles()[$fileName]->getClasses());
        $this->assertEquals('\Pizza', $project->getFiles()[$fileName]->getClasses()['\\Luigi\\Pizza']->getParent());
        $this->assertArrayHasKey(
            '\\Luigi\\Pizza::$instance',
            $project->getFiles()[$fileName]->getClasses()['\\Luigi\\Pizza']->getProperties()
        );

        $methods = $project->getFiles()[$fileName]->getClasses()['\\Luigi\\Pizza']->getMethods();
        $this->assertArrayHasKey(
            '\\Luigi\\Pizza::__construct()',
            $methods
        );

        $this->assertEquals('style', $methods['\\Luigi\\Pizza::__construct()']->getArguments()[0]->getName());
        $this->assertEquals(
            new Object_(new Fqsen('\\Luigi\\Pizza\Style')),
            $methods['\\Luigi\\Pizza::__construct()']->getArguments()[0]->getType()
        );
    }

    public function testDocblockOfMethodIsProcessed() : void
    {
        $fileName = __DIR__ . '/data/Luigi/Pizza.php';
        $project = $this->fixture->create(
            'MyProject',
            [new LocalFile($fileName)]
        );

        $this->assertArrayHasKey($fileName, $project->getFiles());

        $methods = $project->getFiles()[$fileName]->getClasses()['\\Luigi\\Pizza']->getMethods();

        $createInstanceMethod = $methods['\\Luigi\\Pizza::createInstance()'];

        $this->assertInstanceOf(DocBlock::class, $createInstanceMethod->getDocBlock());

        $docblock = $createInstanceMethod->getDocBlock();
        /** @var Param[] $params */
        $params = $docblock->getTagsByName('param');

        /** @var Object_ $objectType */
        $objectType = $params[0]->getType();

        $this->assertEquals(new Fqsen('\Luigi\Pizza\Style'), $objectType->getFqsen());
    }

    public function testWithUsedParent() : void
    {
        $fileName = __DIR__ . '/data/Luigi/StyleFactory.php';
        $project = $this->fixture->create(
            'MyProject',
            [new LocalFile($fileName)]
        );

        $this->assertArrayHasKey($fileName, $project->getFiles());
        $this->assertArrayHasKey('\\Luigi\\StyleFactory', $project->getFiles()[$fileName]->getClasses());
        $this->assertEquals(
            '\\Luigi\\Pizza\\PizzaComponentFactory',
            $project->getFiles()[$fileName]->getClasses()['\\Luigi\\StyleFactory']->getParent()
        );
    }

    public function testWithInterface() : void
    {
        $fileName = __DIR__ . '/data/Luigi/Valued.php';
        $project = $this->fixture->create(
            'MyProject',
            [new LocalFile($fileName)]
        );

        $this->assertArrayHasKey('\\Luigi\\Valued', $project->getFiles()[$fileName]->getInterfaces());
    }

    public function testWithTrait() : void
    {
        $fileName = __DIR__ . '/data/Luigi/ExampleNestedTrait.php';
        $project = $this->fixture->create(
            'MyProject',
            [new LocalFile($fileName)]
        );

        $this->assertArrayHasKey('\\Luigi\\ExampleNestedTrait', $project->getFiles()[$fileName]->getTraits());
    }

    public function testWithGlobalConstants() : void
    {
        $fileName = __DIR__ . '/data/Luigi/constants.php';
        $project = $this->fixture->create(
            'MyProject',
            [new LocalFile($fileName)]
        );

        $this->assertArrayHasKey('\\Luigi\\OVEN_TEMPERATURE', $project->getFiles()[$fileName]->getConstants());
        $this->assertArrayHasKey('\\Luigi\\MAX_OVEN_TEMPERATURE', $project->getFiles()[$fileName]->getConstants());
        $this->assertArrayHasKey('\\OUTSIDE_OVEN_TEMPERATURE', $project->getFiles()[$fileName]->getConstants());
        $this->assertArrayHasKey('\\Luigi_OUTSIDE_OVEN_TEMPERATURE', $project->getFiles()[$fileName]->getConstants());
    }

    public function testInterfaceExtends() : void
    {
        $fileName = __DIR__ . '/data/Luigi/Packing.php';
        $project = $this->fixture->create(
            'MyProject',
            [new LocalFile($fileName)]
        );

        $this->assertArrayHasKey('\\Luigi\\Packing', $project->getFiles()[$fileName]->getInterfaces());
        $interface = current($project->getFiles()[$fileName]->getInterfaces());

        $this->assertEquals(['\\Packing' => new Fqsen('\\Packing')], $interface->getParents());
    }

    public function testMethodReturnType() : void
    {
        $fileName = __DIR__ . '/data/Packing.php';
        $project = $this->fixture->create(
            'MyProject',
            [new LocalFile($fileName)]
        );

        $this->assertArrayHasKey('\\Packing', $project->getFiles()[$fileName]->getInterfaces());
        $interface = current($project->getFiles()[$fileName]->getInterfaces());

        $this->assertEquals(new String_(), $interface->getMethods()['\Packing::getName()']->getReturnType());
    }
}
