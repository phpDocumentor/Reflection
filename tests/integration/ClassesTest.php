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
use phpDocumentor\Reflection\File\LocalFile;
use phpDocumentor\Reflection\Php\Class_;
use phpDocumentor\Reflection\Php\Constant;
use phpDocumentor\Reflection\Php\File as PhpFile;
use phpDocumentor\Reflection\Php\ProjectFactory;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Object_;

/**
 * @coversNothing
 */
final class ClassesTest extends MockeryTestCase
{
    const FILE_PIZZA = __DIR__ . '/data/Pizza.php';
    const FILE_LUIGI_PIZZA = __DIR__ . '/data/Luigi/Pizza.php';
    /** @var ProjectFactory */
    private $fixture;

    /** @var Project */
    private $project;

    protected function setUp() : void
    {
        $this->fixture = ProjectFactory::createInstance();
        $this->project = $this->fixture->create(
            'MyProject',
            [
                new LocalFile(self::FILE_PIZZA),
                new LocalFile(self::FILE_LUIGI_PIZZA),
            ]
        );
    }

    public function testItHasAllConstants() : void
    {
        $file = $this->project->getFiles()[self::FILE_PIZZA];

        $className = '\\Pizza';
        $constantName = '\\Pizza::PACKAGING';

        $class = $this->fetchClassFromFile($className, $file);

        $this->assertArrayHasKey($constantName, $class->getConstants());
        $constant = $class->getConstants()[$constantName];

        $this->assertInstanceOf(Constant::class, $constant);

        $this->assertArrayHasKey('\\OVEN_TEMPERATURE', $file->getConstants());
        $this->assertArrayHasKey('\\MAX_OVEN_TEMPERATURE', $file->getConstants());
    }

    public function testTypedPropertiesReturnTheirType() : void
    {
        $fileName = self::FILE_LUIGI_PIZZA;
        $project = $this->fixture->create(
            'MyProject',
            [new LocalFile($fileName)]
        );

        /** @var Class_ $pizzaClass */
        $pizzaClass = $project->getFiles()[$fileName]->getClasses()['\\Luigi\\Pizza'];
        $this->assertArrayHasKey('\\Luigi\\Pizza::$size', $pizzaClass->getProperties());
        $this->assertEquals(new Integer(), $pizzaClass->getProperties()['\\Luigi\\Pizza::$size']->getType());
    }

    public function testWithNamespacedClass() : void
    {
        $fileName = self::FILE_LUIGI_PIZZA;
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

    private function fetchClassFromFile(string $className, PhpFile $file)
    {
        $this->assertArrayHasKey($className, $file->getClasses());

        return $file->getClasses()[$className];
    }
}
