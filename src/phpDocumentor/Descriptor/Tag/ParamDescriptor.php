<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Tag;

/**
 * Descriptor representing the param tag with a function or method.
 */
class ParamDescriptor extends BaseTypes\TypedVariableAbstract
{
    /** @var bool */
    private $isVariadic = false;

    /**
     * Returns whether this is a variadic parameter.
     *
     * @return boolean
     */
    public function isVariadic()
    {
        return $this->isVariadic;
    }

    /**
     * Registers whether this is a variadic parameter.
     *
     * @param boolean $isVariadic
     *
     * @return void
     */
    public function setIsVariadic($isVariadic)
    {
        $this->isVariadic = $isVariadic;
    }
}
