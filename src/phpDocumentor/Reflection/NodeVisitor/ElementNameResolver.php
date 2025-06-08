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

namespace phpDocumentor\Reflection\NodeVisitor;

use Override;
use phpDocumentor\Reflection\Fqsen;
use PhpParser\Node;
use PhpParser\Node\Const_;
use PhpParser\Node\PropertyItem;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Enum_;
use PhpParser\Node\Stmt\EnumCase;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\PropertyProperty;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use SplDoublyLinkedList;

use function rtrim;

final class ElementNameResolver extends NodeVisitorAbstract
{
    /** @var SplDoublyLinkedList<Node\Identifier|string|null> */
    private SplDoublyLinkedList $parts;

    public function __construct()
    {
        $this->resetState('\\');
    }

    /**
     * Resets the object to a known state before start processing.
     *
     * @inheritDoc
     */
    #[Override]
    public function beforeTraverse(array $nodes)
    {
        $this->resetState('\\');

        return null;
    }

    /**
     * Performs a reset of the added element when needed.
     *
     * @inheritDoc
     */
    #[Override]
    public function leaveNode(Node $node)
    {
        switch ($node::class) {
            case Namespace_::class:
            case Class_::class:
            case Enum_::class:
            case EnumCase::class:
            case ClassMethod::class:
            case Trait_::class:
            case PropertyProperty::class:
            case PropertyItem::class:
            case Node\PropertyItem::class:
            case ClassConst::class:
            case Const_::class:
            case Interface_::class:
            case Function_::class:
                if (!$this->parts->isEmpty()) {
                    $this->parts->pop();
                }

                break;
        }

        return null;
    }

    /**
     * Adds fqsen property to a node when applicable.
     */
    #[Override]
    public function enterNode(Node $node): int|null
    {
        switch ($node::class) {
            case Namespace_::class:
                if ($node->name === null) {
                    break;
                }

                $this->resetState('\\' . $node->name . '\\');
                $this->setFqsen($node);
                break;
            case Class_::class:
            case Trait_::class:
            case Interface_::class:
            case Enum_::class:
                if (empty($node->name)) {
                    return NodeTraverser::DONT_TRAVERSE_CHILDREN;
                }

                $this->parts->push((string) $node->name);
                $this->setFqsen($node);
                break;
            case Function_::class:
                $this->parts->push($node->name . '()');
                $this->setFqsen($node);

                return NodeTraverser::DONT_TRAVERSE_CHILDREN;

            case ClassMethod::class:
                $this->parts->push('::' . $node->name . '()');
                $this->setFqsen($node);

                return NodeTraverser::DONT_TRAVERSE_CHILDREN;

            case ClassConst::class:
                $this->parts->push('::');
                break;
            case Const_::class:
                $this->parts->push($node->name);
                $this->setFqsen($node);
                break;
            case Node\PropertyItem::class:
            case PropertyProperty::class:
                $this->parts->push('::$' . $node->name);
                $this->setFqsen($node);
                break;
            case EnumCase::class:
                $this->parts->push('::' . $node->name);
                $this->setFqsen($node);
                break;
        }

        return null;
    }

    /**
     * Resets the state of the object to an empty state.
     */
    private function resetState(string|null $namespace = null): void
    {
        $this->parts = new SplDoublyLinkedList();
        $this->parts->push($namespace);
    }

    /**
     * Builds the name of the current node using the parts that are pushed to the parts list.
     */
    private function buildName(): string
    {
        $name = '';
        foreach ($this->parts as $part) {
            $name .= $part;
        }

        return rtrim($name, '\\');
    }

    private function setFqsen(Node $node): void
    {
        $fqsen = new Fqsen($this->buildName());
        $node->setAttribute('fqsen', $fqsen);
    }
}
