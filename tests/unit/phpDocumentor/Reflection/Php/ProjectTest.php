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

use phpDocumentor\Reflection\Fqsen;
use PHPUnit\Framework\TestCase;

/**
 * Tests the functionality for the Project class.
 *
 * @coversDefaultClass phpDocumentor\Reflection\Php\Project
 */
class ProjectTest extends TestCase
{
    const EXAMPLE_NAME = 'Initial name';

    /** @var Project */
    private $fixture;

    /**
     * Initializes the fixture object.
     */
    protected function setUp()
    {
        $this->fixture = new Project(self::EXAMPLE_NAME);
    }

    /**
     * @covers ::__construct
     * @covers ::getName
     */
    public function testGetSetName()
    {
        $this->assertEquals(self::EXAMPLE_NAME, $this->fixture->getName());
    }

    /**
     * @covers ::getFiles
     * @covers ::addFile
     */
    public function testGetAddFiles()
    {
        $this->assertEmpty($this->fixture->getFiles());

        $include = new File('foo-bar', 'foo/bar');
        $this->fixture->addFile($include);

        $this->assertSame(['foo/bar' => $include], $this->fixture->getFiles());
    }

    /**
     * @covers ::__construct
     * @covers ::getRootNamespace
     */
    public function testGetRootNamespace()
    {
        $this->assertInstanceOf(Namespace_::class, $this->fixture->getRootNamespace());

        $namespaceDescriptor = new Namespace_(new Fqsen('\MySpace'));
        $project = new Project(self::EXAMPLE_NAME, $namespaceDescriptor);

        $this->assertSame($namespaceDescriptor, $project->getRootNamespace());
    }

    /**
     * @covers ::getNamespaces
     * @covers ::addNamespace
     */
    public function testGetAddNamespace()
    {
        $this->assertEmpty($this->fixture->getNamespaces());

        $namespace = new Namespace_(new Fqsen('\MySpace'));
        $this->fixture->addNamespace($namespace);

        $this->assertSame(['\MySpace' => $namespace], $this->fixture->getNamespaces());
    }
}
