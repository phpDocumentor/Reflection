<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php\Factory;

use OutOfBoundsException;
use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Php\File as FileElement;
use phpDocumentor\Reflection\Php\Project;
use phpDocumentor\Reflection\Php\PropertyHook;
use phpDocumentor\Reflection\Types\Context as TypeContext;

use function array_reverse;
use function end;

final class ContextStack
{
    /** @var (Element|FileElement|PropertyHook)[] */
    private array $elements = [];

    public function __construct(private readonly Project $project, private readonly TypeContext|null $typeContext = null)
    {
    }

    /** @param (Element|FileElement|PropertyHook)[] $elements */
    private static function createFromSelf(Project $project, TypeContext|null $typeContext, array $elements): self
    {
        $self = new self($project, $typeContext);
        $self->elements = $elements;

        return $self;
    }

    public function push(Element|FileElement|PropertyHook $element): self
    {
        $elements = $this->elements;
        $elements[] = $element;

        return self::createFromSelf($this->project, $this->typeContext, $elements);
    }

    public function withTypeContext(TypeContext $typeContext): ContextStack
    {
        return self::createFromSelf($this->project, $typeContext, $this->elements);
    }

    public function getTypeContext(): TypeContext|null
    {
        return $this->typeContext;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function peek(): Element|FileElement|PropertyHook
    {
        $element = end($this->elements);
        if ($element === false) {
            throw new OutOfBoundsException('Stack is empty');
        }

        return $element;
    }

    /**
     * Returns the first element of type.
     *
     * Will reverse search the stack for an element matching $type. Will return null when the element type is not
     * in the current stack.
     *
     * @param class-string $type
     */
    public function search(string $type): Element|FileElement|PropertyHook|null
    {
        $reverseElements = array_reverse($this->elements);
        foreach ($reverseElements as $element) {
            if ($element instanceof $type) {
                return $element;
            }
        }

        return null;
    }
}
