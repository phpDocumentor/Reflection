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

namespace phpDocumentor\Reflection\Php\Factory\Middleware;


use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Middleware\Middleware;
use phpDocumentor\Reflection\Php\Factory\ClassConstantIterator;
use phpDocumentor\Reflection\Php\Factory\PropertyIterator;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Types\Context;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\TraitUse;
use PhpParser\Node\Stmt\Property as PropertyNode;

final class Statements implements Middleware
{
    private $callbacks;

    public function __construct()
    {
        $this->callbacks[TraitUse::class] = function($classElement, $stmt) {
            foreach ($stmt->traits as $use) {
                $classElement->addUsedTrait(new Fqsen('\\'. $use->toString()));
            }
        };

        $this->callbacks[ClassConst::class] = function($classElement, $stmt, $strategies, $context) {
            $constants = new ClassConstantIterator($stmt);
            foreach ($constants as $const) {
                $element = $this->createMember($const, $strategies, $context);
                $classElement->addConstant($element);
            }
        };

        $this->callbacks[ClassMethod::class] = function ($classElement, $stmt, $strategies, $context) {
            $method = $this->createMember($stmt, $strategies, $context);
            $classElement->addMethod($method);
        };

        $this->callbacks[PropertyNode::class] = function ($classElement, $stmt, $strategies, $context) {
            $properties = new PropertyIterator($stmt);
            foreach ($properties as $property) {
                $element = $this->createMember($property, $strategies, $context);
                $classElement->addProperty($element);
            }
        };
    }


    /**
     * Executes this middle ware class.
     *
     * @param $command
     * @param callable $next
     *
     * @return object
     */
    public function execute($command, callable $next)
    {
        $element = $next($command);
        $object = $command->getObject();
        $strategies = $command->getStrategies();
        $context = $command->getContext();

        if (isset($object->stmts)) {
            foreach ($object->stmts as $stmt) {
                if (isset($this->callbacks[get_class($stmt)])) {
                    $callBack = $this->callbacks[get_class($stmt)];
                    $callBack($element, $stmt, $strategies, $context);
                }
            }
        }

        return $element;
    }

    /**
     * @param Node|PropertyIterator|ClassConstantIterator|Doc $stmt
     * @param StrategyContainer $strategies
     * @param Context $context
     * @return Element
     */
    protected function createMember($stmt, StrategyContainer $strategies, Context $context = null)
    {
        $strategy = $strategies->findMatching($stmt);
        return $strategy->create($stmt, $strategies, $context);
    }
}
