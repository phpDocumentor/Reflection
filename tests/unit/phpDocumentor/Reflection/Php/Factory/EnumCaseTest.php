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

namespace phpDocumentor\Reflection\Php\Factory;

use phpDocumentor\Reflection\DocBlockFactoryInterface;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Php\Enum_ as EnumElement;
use phpDocumentor\Reflection\Php\EnumCase as EnumCaseElement;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategies;
use phpDocumentor\Reflection\Php\StrategyContainer;
use PhpParser\Node\Stmt\EnumCase as EnumCaseNode;
use PhpParser\PrettyPrinter\Standard;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use stdClass;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Factory\EnumCase
 * @covers \phpDocumentor\Reflection\Php\Factory\AbstractFactory
 * @covers ::__construct
 * @covers ::<protected>
 * @covers ::<private>
 */
final class EnumCaseTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy $docblockFactory;

    protected function setUp(): void
    {
        $this->docblockFactory = $this->prophesize(DocBlockFactoryInterface::class);
        $this->fixture = new EnumCase($this->docblockFactory->reveal(), new Standard());
    }

    /**
     * @covers ::matches
     */
    public function testMatches(): void
    {
        self::assertFalse($this->fixture->matches(self::createContext(null), new stdClass()));
        self::assertTrue(
            $this->fixture->matches(
                self::createContext(null),
                $this->prophesize(EnumCaseNode::class)->reveal()
            )
        );
    }

    /**
     * @covers ::create
     */
    public function testSimpleCreate(): void
    {
        $containerMock = $this->prophesize(StrategyContainer::class)->reveal();
        $enumMock = $this->buildEnumCaseMock();
        $enumMock->getDocComment()->willReturn(null);

        $result = $this->performCreate($enumMock->reveal());

        self::assertInstanceOf(EnumElement::class, $result);
        self::assertEquals(
            [
                '\Space\MyEnum::VALUE' => new EnumCaseElement(
                    new Fqsen('\Space\MyEnum::VALUE'),
                    null,
                    new Location(1),
                    new Location(2)
                ),
            ],
            $result->getCases()
        );
    }

    private function performCreate(EnumCaseNode $enumCase): EnumElement
    {
        $factory = new ProjectFactoryStrategies([]);
        $enum = new EnumElement(new Fqsen('\myEnum'), null);
        $this->fixture->create(self::createContext(null)->push($enum), $enumCase, $factory);

        return $enum;
    }

    private function buildEnumCaseMock(): ObjectProphecy
    {
        $enumMock = $this->prophesize(EnumCaseNode::class);
        $enumMock->getAttribute('fqsen')->willReturn(new Fqsen('\Space\MyEnum::VALUE'));
        $enumMock->getLine()->willReturn(1);
        $enumMock->getEndLine()->willReturn(2);

        return $enumMock;
    }
}
