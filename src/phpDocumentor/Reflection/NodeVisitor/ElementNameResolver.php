<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */


namespace phpDocumentor\Reflection\NodeVisitor;

use phpDocumentor\Reflection\Fqsen;
use PhpParser\Node;
use PhpParser\Node\Const_;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\PropertyProperty;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\NodeVisitorAbstract;

final class ElementNameResolver extends NodeVisitorAbstract
{
    /**
     * @var \SplDoublyLinkedList
     */
    private $parts = null;

    /**
     * Resets the object to a known state before start processing.
     *
     * @param array $nodes
     */
    public function beforeTraverse(array $nodes)
    {
        $this->resetState('\\');
    }

    /**
     * Performs a reset of the added element when needed.
     *
     * @param Node $node
     */
    public function leaveNode(Node $node)
    {
        switch (get_class($node)) {
            case Namespace_::class:
            case Class_::class:
            case ClassMethod::class:
            case Trait_::class:
            case PropertyProperty::class:
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
     * @param Node $node
     */
    public function enterNode(Node $node)
    {
        switch (get_class($node)) {
            case Namespace_::class:
                $this->resetState('\\' . $node->name . '\\');
                $node->fqsen = new Fqsen($this->buildName());
                break;
            case Class_::class:
            case Trait_::class:
            case Interface_::class:
                $this->parts->push((string)$node->name);
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
            case Const_::class:
                $this->parts->push('::' . $node->name);
                $node->fqsen = new Fqsen($this->buildName());
                break;
            case PropertyProperty::class:
                $this->parts->push('::$' . $node->name);
                $node->fqsen = new Fqsen($this->buildName());
                break;
        }
    }

    /**
     * Resets the state of the object to an empty state.
     *
     * @param string $namespace
     */
    private function resetState($namespace = null)
    {
        $this->parts = new \SplDoublyLinkedList();
        $this->parts->push($namespace);
    }

    /**
     * Builds the name of the current node using the parts that are pushed to the parts list.
     *
     * @return null|string
     */
    private function buildName()
    {
        $name = null;
        foreach ($this->parts as $part) {
            $name .= $part;
        }
        return rtrim($name, '\\');
    }
}