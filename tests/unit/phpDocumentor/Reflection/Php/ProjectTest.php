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

use phpDocumentor\Reflection\Fqsen;
use PHPUnit\Framework\TestCase;

/**
 * Tests the functionality for the Project class.
 *
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Project
 * @covers ::__construct
 * @covers ::<private>
 */
class ProjectTest extends TestCase
{
    public const EXAMPLE_NAME = 'Initial name';

    private Project $fixture;

    /**
     * Initializes the fixture object.
     */
    protected function setUp(): void
    {
        $this->fixture = new Project(self::EXAMPLE_NAME);
    }

    /**
     * @uses \phpDocumentor\Reflection\Php\Namespace_
     *
     * @covers ::getName
     */
    public function testGetSetName(): void
    {
        $this->assertEquals(self::EXAMPLE_NAME, $this->fixture->getName());
    }

    /**
     * @uses \phpDocumentor\Reflection\Php\Namespace_
     * @uses \phpDocumentor\Reflection\Php\File
     *
     * @covers ::getFiles
     * @covers ::addFile
     */
    public function testGetAddFiles(): void
    {
        $this->assertEmpty($this->fixture->getFiles());

        $include = new File('foo-bar', 'foo/bar');
        $this->fixture->addFile($include);

        $this->assertSame(['foo/bar' => $include], $this->fixture->getFiles());
    }

    /**
     * @uses \phpDocumentor\Reflection\Php\Namespace_
     *
     * @covers ::getRootNamespace
     */
    public function testGetRootNamespace(): void
    {
        $this->assertInstanceOf(Namespace_::class, $this->fixture->getRootNamespace());

        $namespaceDescriptor = new Namespace_(new Fqsen('\MySpace'));
        $project             = new Project(self::EXAMPLE_NAME, $namespaceDescriptor);

        $this->assertSame($namespaceDescriptor, $project->getRootNamespace());
    }

    /**
     * @uses \phpDocumentor\Reflection\Php\Namespace_
     *
     * @covers ::getNamespaces
     * @covers ::addNamespace
     */
    public function testGetAddNamespace(): void
    {
        $this->assertEmpty($this->fixture->getNamespaces());

        $namespace = new Namespace_(new Fqsen('\MySpace'));
        $this->fixture->addNamespace($namespace);

        $this->assertSame(['\MySpace' => $namespace], $this->fixture->getNamespaces());
    }
}
