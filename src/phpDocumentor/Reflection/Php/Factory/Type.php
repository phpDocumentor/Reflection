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
use function implode;
use function is_string;
use function sprintf;

final class Type
{
    public function fromPhpParser(Identifier|Name|ComplexType|null $type, Context|null $context = null): TypeElement|null
    {
        if ($type === null) {
            return null;
        }

        return (new TypeResolver())
            ->resolve($this->convertPhpParserTypeToString($type), $context);
    }

    private function convertPhpParserTypeToString(NodeAbstract|string $type): string
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
                $type->types,
            );

            return implode('|', $typesAsStrings);
        }

        if ($type instanceof IntersectionType) {
            $typesAsStrings = array_map(
                fn ($typeObject): string => $this->convertPhpParserTypeToString($typeObject),
                $type->types,
            );

            return implode('&', $typesAsStrings);
        }

        throw new InvalidArgumentException(sprintf('Unsupported complex type %s', $type::class));
    }
}
