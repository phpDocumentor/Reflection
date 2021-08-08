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

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Reflection\Php\Project;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Types\Context;
use stdClass;

/**
 * Base test case for all strategies, to be sure that they check if the can handle objects before handeling them.
 */
abstract class TestCase extends MockeryTestCase
{
    /** @var ProjectFactoryStrategy */
    protected $fixture;

    public static function createContext(?Context $typeContext = null): ContextStack
    {
        return new ContextStack(
            new Project('test'),
            $typeContext
        );
    }

    /**
     * @covers \phpDocumentor\Reflection\Php\Factory\AbstractFactory::create
     */
    public function testCreateThrowsException(): void
    {
        $this->expectException('InvalidArgumentException');
        $this->fixture->create(self::createContext(null), new stdClass(), m::mock(StrategyContainer::class));
    }
}
