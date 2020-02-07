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

use OutOfBoundsException;
use phpDocumentor\Reflection\DocBlock as DocBlockInstance;
use phpDocumentor\Reflection\File as FileSystemFile;
use phpDocumentor\Reflection\Middleware\ChainFactory;
use phpDocumentor\Reflection\Middleware\Middleware;
use phpDocumentor\Reflection\Php\Factory\File\CreateCommand;
use phpDocumentor\Reflection\Php\File as FileElement;
use phpDocumentor\Reflection\Php\File as PhpFile;
use phpDocumentor\Reflection\Php\NodesFactory;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\NamespaceNodeToContext;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_ as ClassNode;
use PhpParser\Node\Stmt\Const_ as ConstantNode;
use PhpParser\Node\Stmt\Declare_ as DeclareNode;
use PhpParser\Node\Stmt\Function_ as FunctionNode;
use PhpParser\Node\Stmt\InlineHTML;
use PhpParser\Node\Stmt\Interface_ as InterfaceNode;
use PhpParser\Node\Stmt\Namespace_ as NamespaceNode;
use PhpParser\Node\Stmt\Trait_ as TraitNode;
use function get_class;
use function in_array;
use function is_array;

/**
 * Strategy to create File element from the provided filename.
 * This class supports extra middle wares to add extra steps to the creation process.
 */
final class File extends AbstractFactory
{
    private const SKIPPED_NODE_TYPES = [
        DeclareNode::class,
        InlineHTML::class,
    ];

    /** @var NodesFactory */
    private $nodesFactory;

    /** @var callable */
    private $middlewareChain;

    /**
     * Initializes the object.
     *
     * @param Middleware[] $middleware
     */
    public function __construct(NodesFactory $nodesFactory, $middleware = [])
    {
        $this->nodesFactory = $nodesFactory;

        $lastCallable = function ($command) {
            return $this->createFile($command);
        };

        $this->middlewareChain = ChainFactory::createExecutionChain($middleware, $lastCallable);
    }

    /**
     * @param mixed $file
     */
    public function matches($file) : bool
    {
        return $file instanceof FileSystemFile;
    }

    /**
     * Creates an File out of the given object.
     *
     * Since an object might contain other objects that need to be converted the $factory is passed so it can be
     * used to create nested Elements.
     *
     * @param FileSystemFile $object path to the file to convert to an File object.
     * @param StrategyContainer $strategies used to convert nested objects.
     *
     * @return PhpFile
     */
    protected function doCreate($object, StrategyContainer $strategies, ?Context $context = null)
    {
        $command = new CreateCommand($object, $strategies);
        $middlewareChain = $this->middlewareChain;

        return $middlewareChain($command);
    }

    private function createFile(CreateCommand $command) : FileElement
    {
        $file = $command->getFile();
        $code = $file->getContents();
        $nodes = $this->nodesFactory->create($code);

        $docBlock = $this->createFileDocBlock(null, $command->getStrategies(), null, $nodes);

        $result = new FileElement(
            $file->md5(),
            $file->path(),
            $code,
            $docBlock
        );

        $this->createElements($nodes, $result, $command->getStrategies(), null);

        return $result;
    }

    /**
     * @param Node[] $nodes
     */
    private function createElements(
        array $nodes,
        FileElement $file,
        StrategyContainer $strategies,
        ?Context $context
    ) : void {
        foreach ($nodes as $node) {
            switch (get_class($node)) {
                case Node\Stmt\If_::class:
                    $this->createElements($node->stmts, $file, $strategies, $context);

                    foreach ($node->elseifs as $subNode) {
                        $this->createElements($subNode->stmts, $file, $strategies, $context);
                    }

                    if ($node->else instanceof Node\Stmt\Else_) {
                        $this->createElements($node->else->stmts, $file, $strategies, $context);
                    }
                    break;
                case Node\Stmt\Expression::class:
                    try {
                        $strategy = $strategies->findMatching($node);
                        $constant = $strategy->create($node, $strategies, $context);
                        $file->addConstant($constant);
                    } catch (OutOfBoundsException $exception) {
                        // ignore, we are only interested when it is a define statement
                    }
                    break;
                case ClassNode::class:
                    $strategy = $strategies->findMatching($node);
                    $class = $strategy->create($node, $strategies, $context);
                    $file->addClass($class);
                    break;
                case ConstantNode::class:
                    $constants = new GlobalConstantIterator($node);
                    foreach ($constants as $constant) {
                        $strategy = $strategies->findMatching($constant);
                        $constant = $strategy->create($constant, $strategies, $context);
                        $file->addConstant($constant);
                    }
                    break;
                case FunctionNode::class:
                    $strategy = $strategies->findMatching($node);
                    $function = $strategy->create($node, $strategies, $context);
                    $file->addFunction($function);
                    break;
                case InterfaceNode::class:
                    $strategy = $strategies->findMatching($node);
                    $interface = $strategy->create($node, $strategies, $context);
                    $file->addInterface($interface);
                    break;
                case NamespaceNode::class:
                    $context = (new NamespaceNodeToContext())($node);
                    $file->addNamespace($node->fqsen);
                    $this->createElements($node->stmts, $file, $strategies, $context);
                    break;
                case TraitNode::class:
                    $strategy = $strategies->findMatching($node);
                    $trait = $strategy->create($node, $strategies, $context);
                    $file->addTrait($trait);
                    break;
            }
        }
    }

    /**
     * @param Node[] $nodes
     */
    protected function createFileDocBlock(
        ?Doc $docBlock = null,
        ?StrategyContainer $strategies = null,
        ?Context $context = null,
        array $nodes = []
    ) : ?DocBlockInstance {
        $node = null;
        foreach ($nodes as $n) {
            if (!in_array(get_class($n), self::SKIPPED_NODE_TYPES)) {
                $node = $n;
                break;
            }
        }

        if (!$node instanceof Node) {
            return null;
        }

        $comments = $node->getAttribute('comments');
        if (!is_array($comments) || empty($comments)) {
            return null;
        }

        $found = 0;
        $firstDocBlock = null;
        foreach ($comments as $comment) {
            if (!$comment instanceof Doc) {
                continue;
            }

            // If current node cannot have a docblock return the first comment as docblock for the file.
            if (!(
                $node instanceof ConstantNode ||
                $node instanceof ClassNode ||
                $node instanceof FunctionNode ||
                $node instanceof InterfaceNode ||
                $node instanceof TraitNode
            )) {
                return $this->createDocBlock($strategies, $comment, $context);
            }

            ++$found;
            if ($firstDocBlock === null) {
                $firstDocBlock = $comment;
            } elseif ($found > 2) {
                break;
            }
        }

        if ($found === 2) {
            return $this->createDocBlock($strategies, $firstDocBlock, $context);
        }

        return null;
    }
}
