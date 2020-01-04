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
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use stdClass;

/**
 * Base test case for all strategies, to be sure that they check if the can handle objects before handeling them.
 */
abstract class TestCase extends MockeryTestCase
{
    /** @var ProjectFactoryStrategy */
    protected $fixture;

    public function testCreateThrowsException() : void
    {
        $this->expectException('InvalidArgumentException');
        $this->fixture->create(new stdClass(), m::mock(StrategyContainer::class));
    }
}
