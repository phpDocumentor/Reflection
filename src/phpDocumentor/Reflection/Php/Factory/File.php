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

use phpDocumentor\Reflection\DocBlock as DocBlockInstance;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use phpDocumentor\Reflection\File as FileSystemFile;
use phpDocumentor\Reflection\Middleware\ChainFactory;
use phpDocumentor\Reflection\Middleware\Middleware;
use phpDocumentor\Reflection\Php\Factory\File\CreateCommand;
use phpDocumentor\Reflection\Php\File as FileElement;
use phpDocumentor\Reflection\Php\NodesFactory;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Types\Context;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_ as ClassNode;
use PhpParser\Node\Stmt\Const_ as ConstantNode;
use PhpParser\Node\Stmt\Declare_ as DeclareNode;
use PhpParser\Node\Stmt\Function_ as FunctionNode;
use PhpParser\Node\Stmt\InlineHTML;
use PhpParser\Node\Stmt\Interface_ as InterfaceNode;
use PhpParser\Node\Stmt\Trait_ as TraitNode;

use function array_merge;
use function get_class;
use function in_array;

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

    private NodesFactory $nodesFactory;

    /** @var callable */
    private $middlewareChain;

    /**
     * Initializes the object.
     *
     * @param Middleware[] $middleware
     */
    public function __construct(
        DocBlockFactoryInterface $docBlockFactory,
        NodesFactory $nodesFactory,
        array $middleware = []
    ) {
        $this->nodesFactory = $nodesFactory;
        parent::__construct($docBlockFactory);

        $lastCallable = fn ($command): FileElement => $this->createFile($command);

        $this->middlewareChain = ChainFactory::createExecutionChain($middleware, $lastCallable);
    }

    public function matches(ContextStack $context, object $object): bool
    {
        return $object instanceof FileSystemFile;
    }

    /**
     * Creates an File out of the given object.
     *
     * Since an object might contain other objects that need to be converted the $factory is passed so it can be
     * used to create nested Elements.
     *
     * @param ContextStack $context used to convert nested objects.
     * @param FileSystemFile $object path to the file to convert to an File object.
     * @param StrategyContainer $strategies used to convert nested objects.
     */
    protected function doCreate(ContextStack $context, object $object, StrategyContainer $strategies): void
    {
        $command = new CreateCommand($context, $object, $strategies);
        $middlewareChain = $this->middlewareChain;

        $file = $middlewareChain($command);
        if ($file === null) {
            return;
        }

        $context->getProject()->addFile($file);
    }

    private function createFile(CreateCommand $command): FileElement
    {
        $file = $command->getFile();
        $code = $file->getContents();
        $nodes = $this->nodesFactory->create($code);

        $docBlock = $this->createFileDocBlock(null, $nodes);

        $result = new FileElement(
            $file->md5(),
            $file->path(),
            $code,
            $docBlock
        );

        $this->createElements($command->getContext()->push($result), $nodes, $command->getStrategies());

        return $result;
    }

    /**
     * @param Node[] $nodes
     */
    private function createElements(
        ContextStack $contextStack,
        array $nodes,
        StrategyContainer $strategies
    ): void {
        foreach ($nodes as $node) {
            $strategy = $strategies->findMatching($contextStack, $node);
            $strategy->create($contextStack, $node, $strategies);
        }
    }

    /**
     * @param Node[] $nodes
     */
    protected function createFileDocBlock(
        ?Context $context = null,
        array $nodes = []
    ): ?DocBlockInstance {
        $node = null;
        $comments = [];
        foreach ($nodes as $n) {
            if (!in_array(get_class($n), self::SKIPPED_NODE_TYPES)) {
                $node = $n;
                break;
            }

            $comments = array_merge($comments, $n->getComments());
        }

        if (!$node instanceof Node) {
            return null;
        }

        $comments = array_merge($comments, $node->getComments());
        if (empty($comments)) {
            return null;
        }

        $found = 0;
        $firstDocBlock = null;
        foreach ($comments as $comment) {
            if (!$comment instanceof Doc) {
                continue;
            }

            // If current node cannot have a docblock return the first comment as docblock for the file.
            if (
                !(
                $node instanceof ConstantNode ||
                $node instanceof ClassNode ||
                $node instanceof FunctionNode ||
                $node instanceof InterfaceNode ||
                $node instanceof TraitNode
                )
            ) {
                return $this->createDocBlock($comment, $context);
            }

            ++$found;
            if ($firstDocBlock === null) {
                $firstDocBlock = $comment;
            } elseif ($found > 2) {
                break;
            }
        }

        if ($found === 2) {
            return $this->createDocBlock($firstDocBlock, $context);
        }

        return null;
    }
}
