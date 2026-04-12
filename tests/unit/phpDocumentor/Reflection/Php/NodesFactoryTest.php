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

use phpDocumentor\Reflection\Exception;
use phpDocumentor\Reflection\NodeVisitor\ElementNameResolver;
use PhpParser\Error;
use PhpParser\NodeTraverser;
use PhpParser\NodeTraverserInterface;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

#[CoversClass(NodesFactory::class)]
final class NodesFactoryTest extends TestCase
{
    use ProphecyTrait;

    /**
     * Tests that an instance of the NodesFactory can be made using its static factory method.
     *
     * Unfortunately, we cannot actually inspect whether all recommended items were instantiated, so I create an example
     * NodesFactory containing what I expected and this test will verify that no regression took place.
     */
    public function testThatAFactoryWithRecommendedComponentsCanBeInstantiated(): void
    {
        $factory = NodesFactory::createInstance();

        $this->assertInstanceOf(NodesFactory::class, $factory);
        $this->assertEquals($this->givenTheExpectedDefaultNodesFactory(), $factory);
    }

    public function testThatCodeGetsConvertedIntoNodes(): void
    {
        $parser = $this->prophesize(Parser::class);
        $parser->parse('this is my code')->willReturn(['parsed code']);

        $nodeTraverser = $this->prophesize(NodeTraverserInterface::class);
        $nodeTraverser->traverse(['parsed code'])->willReturn(['traversed code']);

        $factory = new NodesFactory($parser->reveal(), $nodeTraverser->reveal());

        $result = $factory->create('this is my code');

        $this->assertSame(['traversed code'], $result);
    }

    public function testThatParseErrorIncludesFileAndLine(): void
    {
        $factory = NodesFactory::createInstance();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Syntax error in /some/file.php on line 1:');

        $factory->create('<?php class { }', '/some/file.php');
    }

    public function testThatParseErrorWithoutFilePathOnlyIncludesLine(): void
    {
        $factory = NodesFactory::createInstance();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Syntax error on line 1:');

        $factory->create('<?php class { }');
    }

    public function testThatParseErrorWrapsOriginalException(): void
    {
        $parser = $this->prophesize(Parser::class);
        $parser->parse('bad code')->willThrow(new Error('Unexpected token', ['startLine' => 42]));

        $nodeTraverser = $this->prophesize(NodeTraverserInterface::class);

        $factory = new NodesFactory($parser->reveal(), $nodeTraverser->reveal());

        try {
            $factory->create('bad code', '/path/to/source.php');
            $this->fail('Expected Exception was not thrown');
        } catch (Exception $e) {
            $this->assertSame('Syntax error in /path/to/source.php on line 42: Unexpected token', $e->getMessage());
            $this->assertInstanceOf(Error::class, $e->getPrevious());
        }
    }

    private function givenTheExpectedDefaultNodesFactory(): NodesFactory
    {
        $parser = (new ParserFactory())->createForNewestSupportedVersion();
        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NameResolver());
        $traverser->addVisitor(new ElementNameResolver());

        return new NodesFactory($parser, $traverser);
    }
}
