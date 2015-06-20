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


namespace phpDocumentor\Reflection\Php;

use phpDocumentor\Reflection\FqsenResolver;
use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;

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
     */
    public function __construct()
    {
        $this->parser = new Parser(new Lexer);
        $this->traverser = new NodeTraverser(false);
        $this->traverser->addVisitor(new NameResolver());
        $this->traverser->addVisitor(new FqsenResolver());
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