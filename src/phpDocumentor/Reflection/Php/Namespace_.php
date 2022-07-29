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

namespace phpDocumentor\Reflection\Php;

use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Metadata\MetaDataContainer as MetaDataContainerInterface;

/**
 * Represents a namespace and its children for a project.
 */
// @codingStandardsIgnoreStart
final class Namespace_ implements Element, MetaDataContainerInterface
// codingStandardsIgnoreEnd
{
    use MetadataContainer;

    /**
     * @var Fqsen Full Qualified Structural Element Name
     */
    private Fqsen $fqsen;

    /**
     * @var Fqsen[] fqsen of all functions in this namespace
     */
    private array $functions = [];

    /**
     * @var Fqsen[] fqsen of all constants in this namespace
     */
    private array $constants = [];

    /**
     * @var Fqsen[] fqsen of all classes in this namespace
     */
    private array $classes = [];

    /**
     * @var Fqsen[] fqsen of all interfaces in this namespace
     */
    private array $interfaces = [];

    /**
     * @var Fqsen[] fqsen of all traits in this namespace
     */
    private array $traits = [];

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
     */
    public function getFqsen(): Fqsen
    {
        return $this->fqsen;
    }

    /**
     * Returns the name of the element.
     */
    public function getName(): string
    {
        return $this->fqsen->getName();
    }
}
