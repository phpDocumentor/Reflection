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

namespace phpDocumentor\Reflection\ClassReflector;

use phpDocumentor\Reflection\BaseReflector;
use phpDocumentor\Reflection\ConstantReflector as BaseConstantReflector;
use phpDocumentor\Reflection\DocBlock\Context;
use PhpParser\Node as PHPParser_Node;
use PhpParser\Node\Stmt\ClassConst as PHPParser_Node_Stmt_ClassConst;
use PhpParser\Node\Const_ as PHPParser_Node_Const;

class ConstantReflector extends BaseConstantReflector
{
    /** @var PHPParser_Node_Stmt_ClassConst */
    protected $constant;

    /**
     * Registers the Constant Statement and Node with this reflector.
     *
     * @param PHPParser_Node_Stmt_ClassConst $stmt
     * @param Context                   $context
     * @param PHPParser_Node_Const      $node
     */
    public function __construct(
        PHPParser_Node_Stmt_ClassConst $stmt,
        Context $context,
        PHPParser_Node_Const $node
    ) {
        BaseReflector::__construct($node, $context);
        $this->constant = $stmt;
    }
}
