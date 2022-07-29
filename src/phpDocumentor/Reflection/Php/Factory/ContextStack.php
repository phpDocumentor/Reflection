<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php\Factory;

use OutOfBoundsException;
use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Php\File as FileElement;
use phpDocumentor\Reflection\Php\Project;
use phpDocumentor\Reflection\Types\Context as TypeContext;

use function array_reverse;
use function end;

final class ContextStack
{
    /** @var (Element|FileElement)[] */
    private array $elements = [];

    private ?TypeContext $typeContext;
    private Project $project;

    public function __construct(Project $project, ?TypeContext $typeContext = null)
    {
        $this->project = $project;
        $this->typeContext = $typeContext;
    }

    /** @param (Element|FileElement)[] $elements */
    private static function createFromSelf(Project $project, ?TypeContext $typeContext, array $elements): self
    {
        $self = new self($project, $typeContext);
        $self->elements = $elements;

        return $self;
    }

    /** @param  Element|FileElement $element */
    public function push($element): self
    {
        $elements = $this->elements;
        $elements[] = $element;

        return self::createFromSelf($this->project, $this->typeContext, $elements);
    }

    public function withTypeContext(TypeContext $typeContext): ContextStack
    {
        return self::createFromSelf($this->project, $typeContext, $this->elements);
    }

    public function getTypeContext(): ?TypeContext
    {
        return $this->typeContext;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    /**
     * @return Element|FileElement
     */
    public function peek()
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
     *
     * @return Element|FileElement|null
     */
    public function search(string $type)
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
