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

use Override;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Php\Constant as ConstantElement;
use phpDocumentor\Reflection\Php\Expression;
use phpDocumentor\Reflection\Php\Expression\ExpressionPrinter;
use phpDocumentor\Reflection\Php\File as FileElement;
use phpDocumentor\Reflection\Php\StrategyContainer;
use PhpParser\Node\Stmt\Const_;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;
use Webmozart\Assert\Assert;

use function is_string;

/**
 * Strategy to convert GlobalConstantIterator to ConstantElement
 *
 * @see ConstantElement
 * @see GlobalConstantIterator
 */
final class GlobalConstant extends AbstractFactory
{
    /**
     * Initializes the object.
     */
    public function __construct(DocBlockFactoryInterface $docBlockFactory, private readonly PrettyPrinter $valueConverter)
    {
        parent::__construct($docBlockFactory);
    }

    #[Override]
    public function matches(ContextStack $context, object $object): bool
    {
        return $object instanceof Const_;
    }

    /**
     * Creates an Constant out of the given object.
     *
     * Since an object might contain other objects that need to be converted the $factory is passed so it can be
     * used to create nested Elements.
     *
     * @param ContextStack $context of the created object
     * @param Const_ $object object to convert to an Element
     * @param StrategyContainer $strategies used to convert nested objects.
     */
    #[Override]
    protected function doCreate(
        ContextStack $context,
        object $object,
        StrategyContainer $strategies,
    ): object|null {
        $constants = new GlobalConstantIterator($object);
        $file = $context->peek();
        Assert::isInstanceOf($file, FileElement::class);

        foreach ($constants as $const) {
            $file->addConstant(
                new ConstantElement(
                    $const->getFqsen(),
                    $this->createDocBlock($const->getDocComment(), $context->getTypeContext()),
                    $this->determineValue($const),
                    new Location($const->getLine()),
                    new Location($const->getEndLine()),
                ),
            );
        }

        return null;
    }

    private function determineValue(GlobalConstantIterator $value): Expression
    {
        $expression = $this->valueConverter->prettyPrintExpr($value->getValue());
        if ($this->valueConverter instanceof ExpressionPrinter) {
            $expression = new Expression($expression, $this->valueConverter->getParts());
        }

        if (is_string($expression)) {
            $expression = new Expression($expression, []);
        }

        return $expression;
    }
}
