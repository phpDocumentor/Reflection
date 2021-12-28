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

use phpDocumentor\Reflection\DocBlockFactoryInterface;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Php\Constant as ConstantElement;
use phpDocumentor\Reflection\Php\File as FileElement;
use phpDocumentor\Reflection\Php\StrategyContainer;
use PhpParser\Node\Stmt\Const_;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;
use Webmozart\Assert\Assert;

/**
 * Strategy to convert GlobalConstantIterator to ConstantElement
 *
 * @see ConstantElement
 * @see GlobalConstantIterator
 */
final class GlobalConstant extends AbstractFactory
{
    /** @var PrettyPrinter */
    private $valueConverter;

    /**
     * Initializes the object.
     */
    public function __construct(DocBlockFactoryInterface $docBlockFactory, PrettyPrinter $prettyPrinter)
    {
        $this->valueConverter = $prettyPrinter;
        parent::__construct($docBlockFactory);
    }

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
    protected function doCreate(
        ContextStack $context,
        object $object,
        StrategyContainer $strategies
    ): void {
        $constants = new GlobalConstantIterator($object);
        $file = $context->peek();
        Assert::isInstanceOf($file, FileElement::class);

        foreach ($constants as $const) {
            $file->addConstant(
                new ConstantElement(
                    $const->getFqsen(),
                    $this->createDocBlock($const->getDocComment(), $context->getTypeContext()),
                    $const->getValue() !== null ? $this->valueConverter->prettyPrintExpr($const->getValue()) : null,
                    new Location($const->getLine()),
                    new Location($const->getEndLine())
                )
            );
        }
    }
}
