<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php\Expression;

use phpDocumentor\Reflection\Fqsen;
use PhpParser\Node\Name;
use PhpParser\PrettyPrinter\Standard;

use function md5;

final class ExpressionPrinter extends Standard
{
    /** @var array<Fqsen> */
    private array $parts = [];

    protected function resetState(): void
    {
        parent::resetState();

        $this->parts = [];
    }

    protected function pName(Name $node): string
    {
        $renderedName = parent::pName($node);
        $code = md5($renderedName);

        $placeholder = '{{ PHPDOC' . $code . ' }}';
        $this->parts[$placeholder] = new Fqsen('\\' . $node);

        return $placeholder;
    }

    /**
     * @return array<Fqsen>
     */
    public function getParts(): array
    {
        return $this->parts;
    }
}
