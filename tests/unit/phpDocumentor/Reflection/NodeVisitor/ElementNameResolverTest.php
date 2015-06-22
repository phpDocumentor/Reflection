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


namespace phpDocumentor\Reflection\NodeVisitor;


use PhpParser\Node\Const_;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Namespace_;

/**
 * Testcase for FqsenResolver
 * @coversDefaultClass phpDocumentor\Reflection\NodeVisitor\ElementNameResolver
 * @covers ::<private>
 */
class ElementNameResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ElementNameResolver
     */
    private $fixture;

    protected function setUp()
    {
        $this->fixture = new ElementNameResolver();
        $this->fixture->beforeTraverse([]);
    }

    /**
     * @covers ::enterNode
     */
    public function testFunctionWithoutNamespace()
    {
        $function = new Function_('myFunction');
        $this->fixture->enterNode($function);

        $this->assertEquals('\myFunction()', (string)$function->fqsen);
    }

    /**
     * @covers ::enterNode
     */
    public function testWithClass()
    {
        $class = new Class_('myClass');
        $this->fixture->enterNode($class);

        $this->assertEquals('\myClass', (string)$class->fqsen);
    }

    /**
     * @covers ::enterNode
     */
    public function testClassConstant()
    {
        $const = new Const_('MY_CLASS', new String_('value'));
        $classConst = new ClassConst([$const]);
        $class = new Class_('myClass');

        $this->fixture->enterNode($class);
        $this->fixture->enterNode($classConst);
        $this->fixture->enterNode($const);

        $this->assertEquals('\\myClass::MY_CLASS', (string)$const->fqsen);
    }

    /**
     * @covers ::enterNode
     */
    public function testNamespacedConstant()
    {
        $const = new Const_('MY_CLASS', new String_('value'));
        $namespace = new Namespace_(new Name('name'));

        $this->fixture->enterNode($namespace);
        $this->fixture->enterNode($const);

        $this->assertEquals('\\name\\MY_CLASS', (string)$const->fqsen);
    }
}
