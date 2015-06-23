<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */


namespace phpDocumentor\Reflection\Php\Factory;

use Mockery as m;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\NodesFactory;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Php\File as FileElement;
use phpDocumentor\Reflection\Php\Class_ as ClassElement;
use phpDocumentor\Reflection\Php\Function_ as FunctionElement;
use phpDocumentor\Reflection\Php\Interface_ as InterfaceElement;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_ as ClassNode;
use PhpParser\Node\Stmt\Function_ as FunctionNode;
use PhpParser\Node\Stmt\Interface_ as InterfaceNode;
use PhpParser\Node\Stmt\Namespace_ as NamespaceNode;

/**
 * Test case for \phpDocumentor\Reflection\Php\Factory\File
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Factory\File
 * @covers ::<private>
 * @covers ::__construct
 */
class FileTest extends TestCase
{
    /**
     * @var m\MockInterface
     */
    private $nodesFactoryMock;

    protected function setUp()
    {
        $this->nodesFactoryMock = m::mock(NodesFactory::class);
        $this->fixture = new File($this->nodesFactoryMock);
    }

    /**
     * @covers ::matches
     */
    public function testMatches()
    {
        $this->assertFalse($this->fixture->matches(new \stdClass()));
        $this->assertTrue($this->fixture->matches(__FILE__));
    }

    /**
     * @covers ::create
     */
    public function testFileWithFunction()
    {
        $functionNode = new FunctionNode('myFunction');
        $this->nodesFactoryMock->shouldReceive('create')
            ->with(file_get_contents(__FILE__))
            ->andReturn(
                [
                    $functionNode
                ]
            );

        $containerMock = m::mock(StrategyContainer::class);
        $containerMock->shouldReceive('findMatching->create')
            ->once()
            ->with($functionNode, $containerMock)
            ->andReturn(new FunctionElement(new Fqsen('\myFunction()')));

        /** @var FileElement $file */
        $file = $this->fixture->create(__FILE__, $containerMock);

        $this->assertEquals(__FILE__, $file->getPath());
        $this->assertArrayHasKey('\myFunction()', $file->getFunctions());
    }

    /**
     * @covers ::create
     */
    public function testFileWithClass()
    {
        $classNode = new ClassNode('myClass');
        $this->nodesFactoryMock->shouldReceive('create')
            ->with(file_get_contents(__FILE__))
            ->andReturn(
                [
                    $classNode
                ]
            );

        $containerMock = m::mock(StrategyContainer::class);
        $containerMock->shouldReceive('findMatching->create')
            ->once()
            ->with($classNode, $containerMock)
            ->andReturn(new ClassElement(new Fqsen('\myClass')));

        /** @var FileElement $file */
        $file = $this->fixture->create(__FILE__, $containerMock);

        $this->assertEquals(__FILE__, $file->getPath());
        $this->assertArrayHasKey('\myClass', $file->getClasses());
    }

    /**
     * @covers ::create
     */
    public function testFileWithNamespace()
    {
        $namespaceNode = new NamespaceNode(new Name('mySpace'));
        $namespaceNode->fqsen = new Fqsen('\mySpace');
        $this->nodesFactoryMock->shouldReceive('create')
            ->with(file_get_contents(__FILE__))
            ->andReturn(
                [
                    $namespaceNode
                ]
            );

        $containerMock = m::mock(StrategyContainer::class);

        /** @var FileElement $file */
        $file = $this->fixture->create(__FILE__, $containerMock);

        $this->assertEquals(__FILE__, $file->getPath());
        $this->assertArrayHasKey('mySpace', $file->getNamespaceAliases());
    }

    /**
     * @covers ::create
     */
    public function testFileWithInterface()
    {
        $interfaceNode = new InterfaceNode('myInterface');
        $this->nodesFactoryMock->shouldReceive('create')
            ->with(file_get_contents(__FILE__))
            ->andReturn(
                [
                    $interfaceNode
                ]
            );

        $containerMock = m::mock(StrategyContainer::class);
        $containerMock->shouldReceive('findMatching->create')
            ->once()
            ->with($interfaceNode, $containerMock)
            ->andReturn(new InterfaceElement(new Fqsen('\myInterface')));

        /** @var FileElement $file */
        $file = $this->fixture->create(__FILE__, $containerMock);

        $this->assertEquals(__FILE__, $file->getPath());
        $this->assertArrayHasKey('\myInterface', $file->getInterfaces());
    }
}
