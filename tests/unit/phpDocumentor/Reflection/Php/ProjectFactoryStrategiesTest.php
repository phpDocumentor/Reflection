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

namespace phpDocumentor\Reflection\Php;
use phpDocumentor\Reflection\Php\Factory\DummyFactoryStrategy;

/**
 * Test case for ProjectFactoryStrategies
 *
 * @coversDefaultClass phpDocumentor\Reflection\Php\ProjectFactoryStrategies
 */
class ProjectFactoryStrategiesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::addStrategy
     */
    public function testStrategiesAreChecked()
    {
        new ProjectFactoryStrategies(array(new DummyFactoryStrategy()));
    }

    /**
     * @covers ::findMatching
     * @covers ::<private>
     */
    public function testFindMatching()
    {
        $strategy = new DummyFactoryStrategy();
        $container = new ProjectFactoryStrategies(array($strategy));
        $actual = $container->findMatching(array('aa'));

        $this->assertSame($strategy, $actual);
    }

    /**
     * @covers ::findMatching
     * @covers ::<private>
     *
     * @expectedException \OutOfBoundsException
     */
    public function testCreateThrowsExceptionWhenStrategyNotFound()
    {
        $container = new ProjectFactoryStrategies(array());
        $container->findMatching(array('aa'));
    }
}
