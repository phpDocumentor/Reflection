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
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Php\File as FileElement;
use phpDocumentor\Reflection\Php\StrategyContainer;
use PhpParser\Node\Stmt\Enum_ as EnumNode;
use Webmozart\Assert\Assert;

use function assert;

final class Enum_ extends AbstractFactory
{
    #[Override]
    public function matches(ContextStack $context, object $object): bool
    {
        return $object instanceof EnumNode;
    }

    /** @param EnumNode $object */
    #[Override]
    protected function doCreate(ContextStack $context, object $object, StrategyContainer $strategies): object
    {
        $docBlock = $this->createDocBlock($object->getDocComment(), $context->getTypeContext());

        $fqsen = $object->getAttribute('fqsen');
        Assert::isInstanceOf($fqsen, Fqsen::class);

        $enum = new \phpDocumentor\Reflection\Php\Enum_(
            $fqsen,
            (new Type())->fromPhpParser($object->scalarType),
            $docBlock,
            new Location($object->getLine()),
            new Location($object->getEndLine()),
        );

        foreach ($object->implements as $interfaceClassName) {
            $enum->addInterface(
                new Fqsen('\\' . $interfaceClassName->toString()),
            );
        }

        $file = $context->peek();
        assert($file instanceof FileElement);
        $file->addEnum($enum);

        foreach ($object->stmts as $stmt) {
            $thisContext = $context->push($enum);
            $strategy = $strategies->findMatching($thisContext, $stmt);
            $strategy->create($thisContext, $stmt, $strategies);
        }

        return $enum;
    }
}
