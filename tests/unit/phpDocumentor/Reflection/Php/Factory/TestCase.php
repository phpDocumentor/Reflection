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
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Types\Context;
use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Base test case for all strategies, to be sure that they check if the can handle objects before handeling them.
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * @var ProjectFactoryStrategy
     */
    protected $fixture;

    /**
     * @covers ::create
     *
     * @expectedException \InvalidArgumentException
     */
    public function testCreateThrowsException()
    {
        $this->fixture->create(new \stdClass(), m::mock(StrategyContainer::class));
    }
}
