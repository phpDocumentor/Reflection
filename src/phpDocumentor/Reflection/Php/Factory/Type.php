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

use phpDocumentor\Reflection\Type as TypeElement;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Context;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\UnionType;
use function implode;

final class Type
{
    /**
     * @param Identifier|Name|NullableType|UnionType|null $type
     */
    public function fromPhpParser($type, ?Context $context = null) : ?TypeElement
    {
        if ($type === null) {
            return null;
        }

        $typeResolver = new TypeResolver();
        if ($type instanceof NullableType) {
            return $typeResolver->resolve('?' . $type->type, $context);
        }

        if ($type instanceof UnionType) {
            return $typeResolver->resolve(implode('|', $type->types), $context);
        }

        return $typeResolver->resolve($type->toString(), $context);
    }
}
