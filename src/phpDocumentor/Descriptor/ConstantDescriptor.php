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

namespace phpDocumentor\Descriptor;

use phpDocumentor\Descriptor\Tag\VarDescriptor;

/**
 * Descriptor representing a constant
 */
class ConstantDescriptor extends DescriptorAbstract implements Interfaces\ConstantInterface
{
    /** @var ClassDescriptor|Interface_|null $parent */
    protected $parent;

    /** @var string[]|null $type */
    protected $types;

    /** @var string $value */
    protected $value;

    /**
     * Registers a parent class or interface with this constant.
     *
     * @param ClassDescriptor|Interface_|null $parent
     *
     * @throws \InvalidArgumentException if anything other than a class, interface or null was passed.
     *
     * @return void
     */
    public function setParent($parent)
    {

        if (!$parent instanceof ClassDescriptor && !$parent instanceof Interface_ && $parent !== null) {
            throw new \InvalidArgumentException('Constants can only have an interface or class as parent');
        }

        $fqsen = $parent !== null
            ? $parent->getFullyQualifiedStructuralElementName() . '::' . $this->getName()
            : $this->getName();

        $this->setFullyQualifiedStructuralElementName($fqsen);

        $this->parent = $parent;
    }

    /**
     * @return null|ClassDescriptor|Interface_
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * {@inheritDoc}
     */
    public function setTypes(Collection $types)
    {
        $this->types = $types;
    }

    /**
     * {@inheritDoc}
     */
    public function getTypes()
    {
        if ($this->types === null) {
            $this->types = new Collection();

            /** @var VarDescriptor $var */
            $var = $this->getVar()->get(0);
            if ($var) {
                $this->types = $var->getTypes();
            }
        }

        return $this->types;
    }

    /**
     * {@inheritDoc}
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return Collection
     */
    public function getVar()
    {
        /** @var Collection $var */
        $var = $this->getTags()->get('var', new Collection());
        if ($var->count() != 0) {
            return $var;
        }

        $inheritedElement = $this->getInheritedElement();
        if ($inheritedElement) {
            return $inheritedElement->getVar();
        }

        return new Collection();
    }

    /**
     * Returns the file associated with the parent class, interface or trait when inside a container.
     *
     * @return FileDescriptor
     */
    public function getFile()
    {
        return parent::getFile() ?: $this->getParent()->getFile();
    }

    /**
     * Returns the Constant from which this one should inherit, if any.
     *
     * @return ConstantDescriptor|null
     */
    public function getInheritedElement()
    {
        /** @var ClassDescriptor|Interface_|null $associatedClass */
        $associatedClass = $this->getParent();

        if (($associatedClass instanceof ClassDescriptor || $associatedClass instanceof Interface_)
            && ($associatedClass->getParent() instanceof ClassDescriptor
                || $associatedClass->getParent() instanceof Interface_
            )
        ) {
            /** @var ClassDescriptor|Interface_ $parentClass */
            $parentClass = $associatedClass->getParent();
            return $parentClass->getConstants()->get($this->getName());
        }

        return null;
    }
}
