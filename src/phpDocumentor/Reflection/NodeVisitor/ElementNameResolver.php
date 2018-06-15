<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2018 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\NodeVisitor;

use phpDocumentor\Reflection\Fqsen;
use PhpParser\Node;
use PhpParser\Node\Const_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\PropertyProperty;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use SplDoublyLinkedList;

final class ElementNameResolver extends NodeVisitorAbstract
{
    /**
     * @var SplDoublyLinkedList
     */
    private $parts = null;

    /**
     * Resets the object to a known state before start processing.
     */
    public function beforeTraverse(array $nodes): void
    {
        $this->resetState('\\');
    }

    /**
     * Performs a reset of the added element when needed.
     */
    public function leaveNode(Node $node): void
    {
        switch (get_class($node)) {
            case Namespace_::class:
            case Class_::class:
            case ClassMethod::class:
            case Trait_::class:
            case PropertyProperty::class:
            case ClassConst::class:
            case Const_::class:
            case Interface_::class:
            case Function_::class:
                $this->parts->pop();
                break;
        }
    }

    /**
     * Adds fqsen property to a node when applicable.
     *
     * @todo this method is decorating the Node with an $fqsen property...
     *       since we can't declare it in PhpParser/NodeAbstract,
     *       we should add a decorator class wrapper in Reflection...
     *       that should clear up the PHPSTAN errors about
     *       "access to an undefined property ::$fqsen".
     */
    public function enterNode(Node $node): ?int
    {
        switch (get_class($node)) {
            case Namespace_::class:
                $this->resetState('\\' . $node->name . '\\');
                $node->fqsen = new Fqsen($this->buildName());
                break;
            case Class_::class:
            case Trait_::class:
            case Interface_::class:
                $this->parts->push((string) $node->name);

                if (empty($node->name)) {
                    return NodeTraverser::DONT_TRAVERSE_CHILDREN;
                }

                $node->fqsen = new Fqsen($this->buildName());
                break;
            case Function_::class:
                $this->parts->push($node->name . '()');
                $node->fqsen = new Fqsen($this->buildName());
                break;
            case ClassMethod::class:
                $this->parts->push('::' . $node->name . '()');
                $node->fqsen = new Fqsen($this->buildName());
                break;
            case ClassConst::class:
                $this->parts->push('::');
                break;
            case Const_::class:
                $this->parts->push($node->name);
                $node->fqsen = new Fqsen($this->buildName());
                break;
            case PropertyProperty::class:
                $this->parts->push('::$' . $node->name);
                $node->fqsen = new Fqsen($this->buildName());
                break;
        }

        return null;
    }

    /**
     * Resets the state of the object to an empty state.
     */
    private function resetState(?string $namespace = null): void
    {
        $this->parts = new SplDoublyLinkedList();
        $this->parts->push($namespace);
    }

    /**
     * Builds the name of the current node using the parts that are pushed to the parts list.
     */
    private function buildName(): string
    {
        $name = null;
        foreach ($this->parts as $part) {
            $name .= $part;
        }

        return rtrim($name, '\\');
    }
}
