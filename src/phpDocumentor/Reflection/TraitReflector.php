<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection;

use PhpParser\Node\Stmt\Trait_ as PHPParser_Node_Stmt_Trait;

class TraitReflector extends ClassReflector
{
    /** @var PHPParser_Node_Stmt_Trait */
    protected $node;
}
