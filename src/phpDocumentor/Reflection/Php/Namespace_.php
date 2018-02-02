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

namespace phpDocumentor\Reflection\Php;

use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Fqsen;

/**
 * Represents a namespace and its children for a project.
 */
// @codingStandardsIgnoreStart
final class Namespace_ implements Element
// codingStandardsIgnoreEnd
{
    /**
     * @var Fqsen Full Qualified Structural Element Name
     */
    private $fqsen;

    /** @var Fqsen[] $functions fqsen of all functions in this namespace */
    private $functions = [];

    /** @var Fqsen[] $constants fqsen of all constants in this namespace */
    private $constants = [];

    /** @var Fqsen[] $classes fqsen of all classes in this namespace */
    private $classes = [];

    /** @var Fqsen[] $interfaces fqsen of all interfaces in this namespace */
    private $interfaces = [];

    /** @var Fqsen[] $traits fqsen of all traits in this namespace */
    private $traits = [];

    /**
     * Initializes the namespace.
     */
    public function __construct(Fqsen $fqsen)
    {
        $this->fqsen = $fqsen;
    }

    /**
     * Returns a list of all fqsen of classes in this namespace.
     *
     * @return Fqsen[]
     */
    public function getClasses(): array
    {
        return $this->classes;
    }

    /**
     * Add a class to this namespace.
     */
    public function addClass(Fqsen $class): void
    {
        $this->classes[(string) $class] = $class;
    }

    /**
     * Returns a list of all constants in this namespace.
     *
     * @return Fqsen[]
     */
    public function getConstants(): array
    {
        return $this->constants;
    }

    /**
     * Add a Constant to this Namespace.
     *
     * @param Fqsen|Constant $contant
     */
    public function addConstant(Fqsen $contant): void
    {
        $this->constants[(string) $contant] = $contant;
    }

    /**
     * Returns a list of all functions in this namespace.
     *
     * @return Fqsen[]
     */
    public function getFunctions(): array
    {
        return $this->functions;
    }

    /**
     * Add a function to this namespace.
     */
    public function addFunction(Fqsen $function): void
    {
        $this->functions[(string) $function] = $function;
    }

    /**
     * Returns a list of all interfaces in this namespace.
     *
     * @return Fqsen[]
     */
    public function getInterfaces(): array
    {
        return $this->interfaces;
    }

    /**
     * Add an interface the this namespace.
     */
    public function addInterface(Fqsen $interface): void
    {
        $this->interfaces[(string) $interface] = $interface;
    }

    /**
     * Returns a list of all traits in this namespace.
     *
     * @return Fqsen[]
     */
    public function getTraits(): array
    {
        return $this->traits;
    }

    /**
     * Add a trait to this namespace.
     */
    public function addTrait(Fqsen $trait): void
    {
        $this->traits[(string) $trait] = $trait;
    }

    /**
     * Returns the Fqsen of the element.
     *
     * @return Fqsen
     */
    public function getFqsen(): Fqsen
    {
        return $this->fqsen;
    }

    /**
     * Returns the name of the element.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->fqsen->getName();
    }
}
