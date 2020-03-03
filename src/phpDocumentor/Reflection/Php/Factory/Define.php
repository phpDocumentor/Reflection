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

use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Php\Constant as ConstantElement;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Types\Context;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;
use RuntimeException;
use function sprintf;
use function strpos;

/**
 * Strategy to convert `define` expressions to ConstantElement
 *
 * @see ConstantElement
 * @see GlobalConstantIterator
 */
final class Define extends AbstractFactory
{
    /** @var PrettyPrinter */
    private $valueConverter;

    /**
     * Initializes the object.
     */
    public function __construct(PrettyPrinter $prettyPrinter)
    {
        $this->valueConverter = $prettyPrinter;
    }

    public function matches($object) : bool
    {
        if (!$object instanceof Expression) {
            return false;
        }

        $expression = $object->expr;
        if (!$expression instanceof FuncCall) {
            return false;
        }

        if (!$expression->name instanceof Name) {
            return false;
        }

        if ((string) $expression->name !== 'define') {
            return false;
        }

        return true;
    }

    /**
     * Creates an Constant out of the given object.
     *
     * Since an object might contain other objects that need to be converted the $factory is passed so it can be
     * used to create nested Elements.
     *
     * @param Expression $object object to convert to an Element
     * @param StrategyContainer $strategies used to convert nested objects.
     * @param Context $context of the created object
     *
     * @return ConstantElement
     */
    protected function doCreate($object, StrategyContainer $strategies, ?Context $context = null)
    {
        $expression = $object->expr;
        if (!$expression instanceof FuncCall) {
            throw new RuntimeException(
                'Provided expression is not a function call; this should not happen because the `create` method'
                . ' checks the given object again using `matches`'
            );
        }

        [$name, $value] = $expression->args;

        return new ConstantElement(
            $this->determineFqsen($name),
            $this->createDocBlock($strategies, $object->getDocComment(), $context),
            $this->determineValue($value),
            new Location($object->getLine())
        );
    }

    private function determineValue(?Arg $value) : ?string
    {
        if ($value === null) {
            return null;
        }

        return $this->valueConverter->prettyPrintExpr($value->value);
    }

    private function determineFqsen(Arg $name) : Fqsen
    {
        /** @var String_ $nameString */
        $nameString = $name->value;

        if (strpos($nameString->value, '\\') === false) {
            return new Fqsen(sprintf('\\%s', $nameString->value));
        }

        return new Fqsen(sprintf('%s', $nameString->value));
    }
}
