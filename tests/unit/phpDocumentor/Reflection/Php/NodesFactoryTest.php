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

use phpDocumentor\Reflection\NodeVisitor\ElementNameResolver;
use PhpParser\Lexer\Emulative;
use PhpParser\NodeTraverser;
use PhpParser\NodeTraverserInterface;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\Php\NodesFactory
 * @covers ::__construct
 * @covers ::<private>
 */
final class NodesFactoryTest extends TestCase
{
    use ProphecyTrait;

    /**
     * Tests that an instance of the NodesFactory can be made using its static factory method.
     *
     * Unfortunately, we cannot actually inspect whether all recommended items were instantiated, so I create an example
     * NodesFactory containing what I expected and this test will verify that no regression took place.
     *
     * @covers ::createInstance
     */
    public function testThatAFactoryWithRecommendedComponentsCanBeInstantiated(): void
    {
        $factory = NodesFactory::createInstance();

        $this->assertInstanceOf(NodesFactory::class, $factory);
        $this->assertEquals($this->givenTheExpectedDefaultNodesFactory(), $factory);
    }

    /**
     * @covers ::create
     */
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

    private function givenTheExpectedDefaultNodesFactory(): NodesFactory
    {
        $lexer = new Emulative([
            'usedAttributes' => [
                'comments',
                'startLine',
                'endLine',
                'startFilePos',
                'endFilePos',
            ],
        ]);
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7, $lexer);
        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NameResolver());
        $traverser->addVisitor(new ElementNameResolver());

        return new NodesFactory($parser, $traverser);
    }
}
