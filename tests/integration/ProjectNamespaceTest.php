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

use phpDocumentor\Reflection\File\LocalFile;
use phpDocumentor\Reflection\Php\ProjectFactory;
use PHPUnit\Framework\TestCase;

/**
 * Integration tests to check the correct working of processing a namespace into a project.
 *
 * @coversNothing
 */
class ProjectNamespaceTest extends TestCase
{
    /**
     * @var ProjectFactory
     */
    private $fixture;

    protected function setUp() : void
    {
        $this->fixture = $this->fixture = ProjectFactory::createInstance();
    }

    public function testWithNamespacedClass() : void
    {
        $fileName = __DIR__ . '/data/Luigi/Pizza.php';
        $project = $this->fixture->create(
            'My Project',
            [ new LocalFile($fileName) ]
        );

        $this->assertArrayHasKey($fileName, $project->getFiles());
        $this->assertArrayHasKey('\\Luigi', $project->getNamespaces());
        $this->assertEquals(
            ['\\Luigi\\Pizza' => new Fqsen('\\Luigi\\Pizza')],
            $project->getNamespaces()['\\Luigi']->getClasses()
        );
    }

    public function testWithNamespacedConstant() : void
    {
        $fileName = __DIR__ . '/data/Luigi/constants.php';
        $project = $this->fixture->create(
            'My Project',
            [ new LocalFile($fileName) ]
        );

        $this->assertArrayHasKey($fileName, $project->getFiles());
        $this->assertArrayHasKey('\\Luigi', $project->getNamespaces());
        $this->assertEquals(
            [
                '\\Luigi\\OVEN_TEMPERATURE' => new Fqsen('\\Luigi\\OVEN_TEMPERATURE'),
                '\\Luigi\\MAX_OVEN_TEMPERATURE' => new Fqsen('\\Luigi\\MAX_OVEN_TEMPERATURE'),
                ],
            $project->getNamespaces()['\\Luigi']->getConstants()
        );
    }
}
