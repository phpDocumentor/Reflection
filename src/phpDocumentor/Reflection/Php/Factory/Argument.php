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

use phpDocumentor\Reflection\Php\Argument as ArgumentDescriptor;
use phpDocumentor\Reflection\Php\Function_;
use phpDocumentor\Reflection\Php\Method;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Param;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;
use Webmozart\Assert\Assert;

/**
 * Strategy to convert Param to Argument
 *
 * @see ArgumentDescriptor
 * @see \PhpParser\Node\Arg
 */
final class Argument extends AbstractFactory implements ProjectFactoryStrategy
{
    private PrettyPrinter $valueConverter;

    /**
     * Initializes the object.
     */
    public function __construct(PrettyPrinter $prettyPrinter)
    {
        $this->valueConverter = $prettyPrinter;
    }

    public function matches(ContextStack $context, object $object): bool
    {
        return $object instanceof Param;
    }

    /**
     * Creates an ArgumentDescriptor out of the given object.
     *
     * Since an object might contain other objects that need to be converted the $factory is passed so it can be
     * used to create nested Elements.
     *
     * @param ContextStack $context of the created object
     * @param Param $object object to convert to an Element
     * @param StrategyContainer $strategies used to convert nested objects.
     */
    protected function doCreate(
        ContextStack $context,
        object $object,
        StrategyContainer $strategies
    ): void {
        Assert::isInstanceOf($object, Param::class);
        Assert::isInstanceOf($object->var, Variable::class);

        $method = $context->peek();
        Assert::isInstanceOfAny(
            $method,
            [
                Method::class,
                Function_::class,
            ]
        );

        $method->addArgument(
            new ArgumentDescriptor(
                (string) $object->var->name,
                (new Type())->fromPhpParser($object->type),
                $object->default !== null ? $this->valueConverter->prettyPrintExpr($object->default) : null,
                $object->byRef,
                $object->variadic
            )
        );
    }
}
