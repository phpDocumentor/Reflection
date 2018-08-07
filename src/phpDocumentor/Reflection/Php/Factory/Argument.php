<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2018 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php\Factory;

use phpDocumentor\Reflection\Php\Argument as ArgumentDescriptor;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\PrettyPrinter;
use phpDocumentor\Reflection\Types\Context;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Param;
use Webmozart\Assert\Assert;

/**
 * Strategy to convert Param to Argument
 *
 * @see \phpDocumentor\Descriptor\Argument
 * @see \PhpParser\Node\Arg
 */
final class Argument extends AbstractFactory implements ProjectFactoryStrategy
{
    /**
     * @var PrettyPrinter
     */
    private $valueConverter;

    /**
     * Initializes the object.
     */
    public function __construct(PrettyPrinter $prettyPrinter)
    {
        $this->valueConverter = $prettyPrinter;
    }

    public function matches($object): bool
    {
        return $object instanceof Param;
    }

    /**
     * Creates an ArgumentDescriptor out of the given object.
     * Since an object might contain other objects that need to be converted the $factory is passed so it can be
     * used to create nested Elements.
     *
     * @param Param $object object to convert to an Element
     * @param StrategyContainer $strategies used to convert nested objects.
     * @param Context $context of the created object
     * @return ArgumentDescriptor
     */
    protected function doCreate($object, StrategyContainer $strategies, ?Context $context = null)
    {
        Assert::isInstanceOf($object, Param::class);
        Assert::isInstanceOf($object->var, Variable::class);
        $default = null;
        if ($object->default !== null) {
            $default = $this->valueConverter->prettyPrintExpr($object->default);
        }

        return new ArgumentDescriptor((string)$object->var->name, $default, $object->byRef, $object->variadic);
    }
}
