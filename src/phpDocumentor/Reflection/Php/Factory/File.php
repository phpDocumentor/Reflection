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

namespace phpDocumentor\Reflection\Php\Factory;

use phpDocumentor\Reflection\DocBlock as DocBlockInstance;
use phpDocumentor\Reflection\File as FileSystemFile;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Middleware\ChainFactory;
use phpDocumentor\Reflection\Middleware\Middleware;
use phpDocumentor\Reflection\Php\Factory\File\CreateCommand;
use phpDocumentor\Reflection\Php\File as FileElement;
use phpDocumentor\Reflection\Php\NodesFactory;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\ContextFactory;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_ as ClassNode;
use PhpParser\Node\Stmt\Function_ as FunctionNode;
use PhpParser\Node\Stmt\Interface_ as InterfaceNode;
use PhpParser\Node\Stmt\Namespace_ as NamespaceNode;
use PhpParser\Node\Stmt\Trait_ as TraitNode;

/**
 * Strategy to create File element from the provided filename.
 * This class supports extra middle wares to add extra steps to the creation process.
 */
final class File extends AbstractFactory implements ProjectFactoryStrategy
{
    /**
     * @var NodesFactory
     */
    private $nodesFactory;

    /**
     * @var callable
     */
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

    public function matches($file): bool
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
     * @return File
     */
    protected function doCreate($object, StrategyContainer $strategies, ?Context $context = null)
    {
        $command = new CreateCommand($object, $strategies);
        $middlewareChain = $this->middlewareChain;

        return $middlewareChain($command);
    }

    /**
     * @return FileElement
     */
    private function createFile(CreateCommand $command)
    {
        $file = $command->getFile();
        $code = $file->getContents();
        $nodes = $this->nodesFactory->create($code);

        $contextFactory = new ContextFactory();
        $context = $contextFactory->createForNamespace('\\', $code);

        $docBlock = $this->createFileDocBlock(null, $command->getStrategies(), $context, $nodes);

        $result = new FileElement(
            $file->md5(),
            $file->path(),
            $code,
            $docBlock
        );

        $this->createElements(new Fqsen('\\'), $nodes, $result, $command->getStrategies());

        return $result;
    }

    /**
     * @param Node[] $nodes
     */
    private function createElements(Fqsen $namespace, array $nodes, FileElement $file, StrategyContainer $strategies): void
    {
        $contextFactory = new ContextFactory();
        $context = $contextFactory->createForNamespace((string) $namespace, $file->getSource());
        foreach ($nodes as $node) {
            switch (get_class($node)) {
                case ClassNode::class:
                    $strategy = $strategies->findMatching($node);
                    $class = $strategy->create($node, $strategies, $context);
                    $file->addClass($class);
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
                    $file->addNamespace($node->fqsen);
                    $this->createElements($node->fqsen, $node->stmts, $file, $strategies);
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
    ): ?DocBlockInstance {
        $node = current($nodes);
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

            //If current node cannot have a docblock return the first comment as docblock for the file.
            if (!(
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
