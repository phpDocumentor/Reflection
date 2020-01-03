<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php;

use phpDocumentor\Reflection\Php\Factory\DummyFactoryStrategy;
use PHPUnit\Framework\TestCase;

/**
 * Test case for ProjectFactoryStrategies
 *
 * @coversDefaultClass phpDocumentor\Reflection\Php\ProjectFactoryStrategies
 */
class ProjectFactoryStrategiesTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::addStrategy
     */
    public function testStrategiesAreChecked() : void
    {
        new ProjectFactoryStrategies([new DummyFactoryStrategy()]);
        $this->assertTrue(true);
    }

    /**
     * @covers ::findMatching
     * @covers ::<private>
     */
    public function testFindMatching() : void
    {
        $strategy  = new DummyFactoryStrategy();
        $container = new ProjectFactoryStrategies([$strategy]);
        $actual    = $container->findMatching(['aa']);

        $this->assertSame($strategy, $actual);
    }

    /**
     * @covers ::findMatching
     * @covers ::<private>
     */
    public function testCreateThrowsExceptionWhenStrategyNotFound() : void
    {
        $this->expectException('OutOfBoundsException');
        $container = new ProjectFactoryStrategies([]);
        $container->findMatching(['aa']);
    }
}
