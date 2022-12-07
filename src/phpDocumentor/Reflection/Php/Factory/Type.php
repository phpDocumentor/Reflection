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

use InvalidArgumentException;
use phpDocumentor\Reflection\Type as TypeElement;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Context;
use PhpParser\Node\ComplexType;
use PhpParser\Node\Identifier;
use PhpParser\Node\IntersectionType;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\UnionType;
use PhpParser\NodeAbstract;

use function array_map;
use function get_class;
use function implode;
use function is_string;
use function sprintf;

final class Type
{
    /**
     * @param Identifier|Name|ComplexType|null $type
     */
    public function fromPhpParser($type, ?Context $context = null): ?TypeElement
    {
        if ($type === null) {
            return null;
        }

        return (new TypeResolver())
            ->resolve($this->convertPhpParserTypeToString($type), $context);
    }

    /**
     * @param NodeAbstract|string $type
     */
    private function convertPhpParserTypeToString($type): string
    {
        if (is_string($type)) {
            return $type;
        }

        if ($type instanceof Identifier) {
            return $type->toString();
        }

        if ($type instanceof Name) {
            return $type->toString();
        }

        if ($type instanceof NullableType) {
            return '?' . $this->convertPhpParserTypeToString($type->type);
        }

        if ($type instanceof UnionType) {
            $typesAsStrings = array_map(
                fn ($typeObject): string => $this->convertPhpParserTypeToString($typeObject),
                $type->types
            );

            return implode('|', $typesAsStrings);
        }

        if ($type instanceof IntersectionType) {
            $typesAsStrings = array_map(
                fn ($typeObject): string => $this->convertPhpParserTypeToString($typeObject),
                $type->types
            );

            return implode('&', $typesAsStrings);
        }

        throw new InvalidArgumentException(sprintf('Unsupported complex type %s', get_class($type)));
    }
}
