<?php
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


namespace phpDocumentor\Reflection\Php;

use phpDocumentor\Reflection\NodeVisitor\ElementNameResolver;
use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use PhpParser\ParserFactory;

/**
 * Factory to create a array of nodes from a provided file.
 * This factory will use PhpParser and NodeTraverser to do the real processing.
 */
class NodesFactory
{
    /**
     * Parser used to parse the code to nodes.
     *
     * @var Parser
     */
    private $parser;

    /**
     * Containing a number of visitors to do some post processing steps on nodes.
     *
     * @var NodeTraverser
     */
    private $traverser;

    /**
     * Initializes the object.
     *
     * @param Parser $parser used to parse the code
     * @param NodeTraverser $traverser used to do some post processing on the nodes
     */
    public function __construct(Parser $parser, NodeTraverser $traverser)
    {
        $this->parser = $parser;
        $this->traverser = $traverser;
    }

    /**
     * Creates a new instance of NodeFactory with default Parser ands Traverser.
     *
     * @param int $kind One of ParserFactory::PREFER_PHP7,
     *  ParserFactory::PREFER_PHP5, ParserFactory::ONLY_PHP7 or ParserFactory::ONLY_PHP5
     * @return static
     */
    public static function createInstance($kind = ParserFactory::PREFER_PHP7)
    {
        $parser = (new ParserFactory)->create($kind);
        $traverser = new NodeTraverser(false);
        $traverser->addVisitor(new NameResolver());
        $traverser->addVisitor(new ElementNameResolver());
        return new static($parser, $traverser);
    }

    /**
     * Will convert the provided code to nodes.
     *
     * @param string $code code to process.
     * @return \PhpParser\Node[]
     */
    public function create($code)
    {
        $stmt = $this->parser->parse($code);
        return $this->traverser->traverse($stmt);
    }
}
