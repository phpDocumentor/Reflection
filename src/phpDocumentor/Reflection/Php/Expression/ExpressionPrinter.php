<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php\Expression;

use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Expression;
use phpDocumentor\Reflection\Type;
use PhpParser\Node\Name;
use PhpParser\PrettyPrinter\Standard;

final class ExpressionPrinter extends Standard
{
    /** @var array<string, Fqsen|Type> */
    private array $parts = [];

    protected function resetState(): void
    {
        parent::resetState();

        $this->parts = [];
    }

    protected function pName(Name $node): string
    {
        $renderedName = parent::pName($node);
        $placeholder = Expression::generatePlaceholder($renderedName);
        $this->parts[$placeholder] = new Fqsen('\\' . $renderedName);

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

    /**
     * @return array<string, Fqsen|Type>
     */
    public function getParts(): array
    {
        return $this->parts;
    }
}
