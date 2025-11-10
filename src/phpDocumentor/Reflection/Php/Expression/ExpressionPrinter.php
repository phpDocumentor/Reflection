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

namespace phpDocumentor\Reflection\Php\Expression;

use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\Php\Expression;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Context;
use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PhpParser\PrettyPrinter\Standard;

final class ExpressionPrinter extends Standard
{
    /** @var array<string, Fqsen|Type> */
    private array $parts = [];
    private Context|null $context = null;
    private FqsenResolver $fqsenResolver;

    /** {@inheritDoc} */
    public function __construct(array $options = [])
    {
        parent::__construct($options);

        $this->fqsenResolver = new FqsenResolver();
    }

    protected function resetState(): void
    {
        parent::resetState();

        $this->parts = [];
    }

    public function prettyPrintExpr(Expr $node, Context|null $context = null): string
    {
        $this->context = $context;

        return parent::prettyPrintExpr($node);
    }

    protected function pName(Name $node): string
    {
        $renderedName = $this->fqsenResolver->resolve(parent::pName($node), $this->context);
        $placeholder = Expression::generatePlaceholder((string) $renderedName);
        $this->parts[$placeholder] = $renderedName;

        return $placeholder;
    }

    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    protected function pName_FullyQualified(Name\FullyQualified $node): string
    {
        $renderedName = parent::pName_FullyQualified($node);
        $placeholder = Expression::generatePlaceholder($renderedName);
        $this->parts[$placeholder] = new Fqsen($renderedName);

        return $placeholder;
    }

    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    protected function pExpr_ClassConstFetch(Expr\ClassConstFetch $node): string
    {
        $renderedName = parent::pObjectProperty($node->name);
        $className = $node->class instanceof Name ? parent::pName($node->class) : $this->p($node->class);
        $placeholder = Expression::generatePlaceholder((string) $renderedName);
        $this->parts[$placeholder] = new Fqsen(
            '\\' . $className . '::' . $renderedName,
        );

        return $placeholder;
    }

    /** @return array<string, Fqsen|Type> */
    public function getParts(): array
    {
        return $this->parts;
    }
}
