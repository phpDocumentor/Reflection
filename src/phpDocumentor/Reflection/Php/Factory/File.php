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


namespace phpDocumentor\Reflection\Php\Factory;

use InvalidArgumentException;
use phpDocumentor\Reflection\Php\File as FileElement;
use phpDocumentor\Reflection\Php\NodesFactory;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use PhpParser\Lexer;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_ as ClassNode;
use PhpParser\Node\Stmt\Function_ as FunctionNode;
use PhpParser\Node\Stmt\Interface_ as InterfaceNode;
use PhpParser\Node\Stmt\Namespace_ as NamespaceNode;
use PhpParser\Node\Stmt\Trait_ as TraitNode;

/**
 * Strategy to create File element from the provided filename.
 */
final class File implements ProjectFactoryStrategy
{
    /**
     * @var NodesFactory
     */
    private $nodesFactory;

    /**
     * Initializes the object
     * @param NodesFactory $nodesFactory
     */
    public function __construct(NodesFactory $nodesFactory)
    {
        $this->nodesFactory = $nodesFactory;
    }

    /**
     * Returns true when the strategy is able to handle the object.
     *
     * @param object $object object to check.
     * @return boolean
     */
    public function matches($object)
    {
        return is_string($object) && file_exists($object);
    }

    /**
     * Creates an File out of the given object.
     * Since an object might contain other objects that need to be converted the $factory is passed so it can be
     * used to create nested Elements.
     *
     * @param string $object object to convert to an File
     * @param StrategyContainer $strategies used to convert nested objects.
     * @return File
     *
     * @throws InvalidArgumentException when this strategy is not able to handle $object or if the file path is not readable.
     */
    public function create($object, StrategyContainer $strategies)
    {
        if (!$this->matches($object)) {
            throw new InvalidArgumentException(
                sprintf('%s cannot handle objects with the type %s',
                    __CLASS__,
                    is_object($object) ? get_class($object) : gettype($object)
                )
            );
        }
        $code = file_get_contents($object);
        $nodes = $this->nodesFactory->create($code);

        $file = new FileElement(md5_file($object), $object, $code);

        $this->createElements($nodes, $file, $strategies);

        return $file;
    }

    /**
     * @param Node[] $nodes
     * @param FileElement $file
     * @param StrategyContainer $strategies
     */
    private function createElements($nodes, FileElement $file, StrategyContainer $strategies)
    {
        foreach ($nodes as $node) {
            switch (get_class($node)) {
                case ClassNode::class:
                    $strategy = $strategies->findMatching($node);
                    $class = $strategy->create($node, $strategies);
                    $file->addClass($class);
                    break;
                case FunctionNode::class:
                    $strategy = $strategies->findMatching($node);
                    $function = $strategy->create($node, $strategies);
                    $file->addFunction($function);
                    break;
                case InterfaceNode::class:
                    $strategy = $strategies->findMatching($node);
                    $interface = $strategy->create($node, $strategies);
                    $file->addInterface($interface);
                    break;
                case NamespaceNode::class:
                    $file->addNamespaceAlias($node->fqsen->getName(), $node->fqsen);
                    $this->createElements($node->stmts, $file, $strategies);
                    break;
                case TraitNode::class:
                    $strategy = $strategies->findMatching($node);
                    $trait = $strategy->create($node, $strategies);
                    $file->addTrait($trait);
                    break;
            }
        }
    }
}
