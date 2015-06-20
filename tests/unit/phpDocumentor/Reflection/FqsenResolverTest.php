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


namespace phpDocumentor\Reflection;


use PhpParser\Node\Stmt\Function_;

/**
 * Testcase for FqsenResolver
 * @coversDefaultClass phpDocumentor\Reflection\FqsenResolver
 * @covers ::<private>
 */
class FqsenResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FqsenResolver
     */
    private $fixture;

    protected function setUp()
    {
        $this->fixture = new FqsenResolver();
    }

    /**
     * @covers ::enterNode
     */
    public function testFunctionWithoutNamespace()
    {
        $resolver = new FqsenResolver();

        $function = new Function_('myFunction');
        $resolver->beforeTraverse(array($function));
        $resolver->enterNode($function);

        $this->assertEquals('\::myFunction()', (string)$function->fqsen);
    }
}
