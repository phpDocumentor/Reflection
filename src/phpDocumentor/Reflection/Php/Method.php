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

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Php\Visibility;

/**
 * Descriptor representing a Method in a Class, Interface or Trait.
 */
final class Method implements Element
{
    /**
     * @var DocBlock|null documentation of this method.
     */
    private $docBlock = null;

    /**
     * @var Fqsen Full Qualified Structural Element Name
     */
    private $fqsen;

    /** @var bool $abstract */
    private $abstract = false;

    /** @var bool $final */
    private $final = false;

    /** @var bool $static */
    private $static = false;

    /** @var Visibility visibility of this method */
    private $visibility = null;

    /** @var Argument[] */
    private $arguments = array();

    /**
     * Initializes the all properties.
     *
     * @param Fqsen $fqsen
     * @param Visibility|null $visibility when null is provided a default 'public' is set.
     * @param DocBlock|null $docBlock
     * @param bool $abstract
     * @param bool $static
     * @param bool $final
     */
    public function __construct(
        Fqsen $fqsen,
        Visibility $visibility = null,
        DocBlock $docBlock = null,
        $abstract = false,
        $static = false,
        $final = false
    )
    {
        $this->fqsen = $fqsen;
        $this->visibility = $visibility;
        $this->docBlock = $docBlock;

        if ($this->visibility === null) {
            $this->visibility = new Visibility('public');
        }

        $this->abstract = $abstract;
        $this->static = $static;
        $this->final = $final;
    }

    /**
     * Returns true when this method is abstract. Otherwise returns false.
     *
     * @return bool
     */
    public function isAbstract()
    {
        return $this->abstract;
    }

    /**
     * Returns true when this method is final. Otherwise returns false.
     *
     * @return bool
     */
    public function isFinal()
    {
        return $this->final;
    }

    /**
     * Returns true when this method is static. Otherwise returns false.
     *
     * @return bool
     */
    public function isStatic()
    {
        return $this->static;
    }

    /**
     * Returns the Visibility of this method.
     *
     * @return Visibility
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * Returns the arguments of this method.
     *
     * @return Argument[]
     */
    public function getArguments()
    {
        return $this->arguments;
    }


    /**
     * Add new argument to this method.
     *
     * @param Argument $argument
     * @return void
     */
    public function addArgument(Argument $argument)
    {
        $this->arguments[] = $argument;
    }

    /**
     * Returns the Fqsen of the element.
     *
     * @return Fqsen
     */
    public function getFqsen()
    {
        return $this->fqsen;
    }

    /**
     * Returns the name of the element.
     *
     * @return string
     */
    public function getName()
    {
        return $this->fqsen->getName();
    }

    /**
     * Returns the DocBlock of this method if available.
     *
     * @returns null|DocBlock
     */
    public function getDocblock()
    {
        return $this->docBlock;
    }
}
