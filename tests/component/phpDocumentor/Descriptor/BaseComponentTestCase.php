<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

use phpDocumentor\Reflection\FileReflector;

abstract class BaseComponentTestCase extends \PHPUnit_Framework_TestCase
{
    /** @var ProjectDescriptor */
    protected $projectDescriptor;

    /** @var string */
    protected $filename;

    /** @var Analyzer $fixture */
    protected $fixture;

    public function switchBetweenStrategies()
    {
        return array(
            'PhpParser' => $this->createProjectDescriptorUsingAnalyzer('phpparser'),
            'Reflector' => $this->createProjectDescriptorUsingAnalyzer('reflector')
        );
    }

    /**
     * @return string
     */
    protected function createProjectDescriptorUsingAnalyzer($strategy = 'phpparser')
    {
        $this->filename = __DIR__ . '/../../../example.file.php';
        $this->fixture = Analyzer::create();

        switch ($strategy) {
            case 'phpparser':
                $data = new \SplFileObject($this->filename);
                break;
            case 'reflector':
                $data = new FileReflector($this->filename);
                $data->process();
                break;
            default:
                throw new \InvalidArgumentException("Strategy '$strategy' is not supported");
        }

        $this->fixture->analyze($data);
        $this->projectDescriptor = $this->fixture->getProjectDescriptor();
        return array($this->projectDescriptor, $this->filename);
    }

    /**
     * @param $class
     * @param $message
     */
    protected function assertSummary($class, $message)
    {
        $this->assertSame($message, $class->getSummary());
    }

    /**
     * @param $class
     * @param $message
     */
    protected function assertDescription($class, $message)
    {
        $this->assertSame($message, $class->getDescription());
    }

    /**
     * @param $class
     * @param $name
     */
    protected function assertName($class, $name)
    {
        $this->assertSame($name, $class->getName());
    }

    /**
     * @param $class
     * @param $fqsen
     */
    protected function assertFullyQualifiedStructuralElementName($class, $fqsen)
    {
        $this->assertSame($fqsen, $class->getFullyQualifiedStructuralElementName());
    }

    /**
     * @param $class
     * @param $namespace
     */
    protected function assertNamespace($class, $namespace)
    {
        $this->assertSame($namespace, $class->getNamespace());
    }

    /**
     * @param $class
     * @param $parent
     */
    protected function assertParentElement($class, $parent)
    {
        $this->assertSame($parent, $class->getParent());
    }

    /**
     * @param FileDescriptor $file
     */
    protected function assertIsFile(FileDescriptor $file)
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\FileDescriptor', $file);
    }

    /**
     * @param $class
     */
    protected function assertIsClass($class)
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\ClassDescriptor', $class);
    }

    /**
     * @param DescriptorAbstract $file
     * @param string $package
     */
    protected function assertPackageName(DescriptorAbstract $file, $package)
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $file->getTags()->get('package'));
        $this->assertInstanceOf('phpDocumentor\Descriptor\TagDescriptor', $file->getTags()->get('package')->get(0));
        $this->assertSame($package, $file->getTags()->get('package')->get(0)->getDescription());
    }

    /**
     * @param $file
     * @param $className
     * @return string
     */
    protected function assertFileHasClass($file, $className)
    {
        $classClassname = 'phpDocumentor\Descriptor\ClassDescriptor';
        $this->assertInstanceOf($classClassname, $file->getClasses()->get($className));
    }

    /**
     * @param $file
     * @param $interfaceName
     * @return string
     */
    protected function assertFileHasInterface($file, $interfaceName)
    {
        $classClassname = 'phpDocumentor\Descriptor\InterfaceDescriptor';
        $this->assertInstanceOf($classClassname, $file->getInterfaces()->get($interfaceName));
    }

    /**
     * @param $file
     * @param $traitName
     * @return string
     */
    protected function assertFileHasTrait($file, $traitName)
    {
        $classClassname = 'phpDocumentor\Descriptor\TraitDescriptor';
        $this->assertInstanceOf($classClassname, $file->getTraits()->get($traitName));
    }

    /**
     * @param $file
     * @param $constantName
     * @return string
     */
    protected function assertFileHasConstant($file, $constantName)
    {
        $classClassname = 'phpDocumentor\Descriptor\ConstantDescriptor';
        $this->assertInstanceOf($classClassname, $file->getConstants()->get($constantName));
    }

    /**
     * @param $class
     * @param $interfaceName
     */
    protected function assertHasInterface($class, $interfaceName)
    {
        $this->assertSame($interfaceName, $class->getInterfaces()->get($interfaceName));
    }

    /**
     * @param $class
     * @param $name
     */
    protected function assertHasProperty($class, $name, $fqsen)
    {
        $property = $class->getProperties()->get($name);
        $this->assertInstanceOf('phpDocumentor\Descriptor\PropertyDescriptor', $property);

        $this->assertName($property, $name);
        $this->assertFullyQualifiedStructuralElementName($property, $fqsen);

        return $property;
    }

    /**
     * @param $class
     * @param $name
     */
    protected function assertHasMethod($class, $name, $fqsen)
    {
        $method = $class->getMethods()->get($name);
        $this->assertInstanceOf('phpDocumentor\Descriptor\MethodDescriptor', $method);

        $this->assertName($method, $name);
        $this->assertFullyQualifiedStructuralElementName($method, $fqsen);

        return $method;
    }

    /**
     * @return FileDescriptor
     */
    protected function getFileDescriptor()
    {
        return $this->projectDescriptor->getFiles()->get($this->filename);
    }

    /**
     * @param $className
     * @return ClassDescriptor|null
     */
    protected function fetchClass($className)
    {
        /** @var FileDescriptor $file */
        $file = $this->projectDescriptor->getFiles()->get($this->filename);

        return $file->getClasses()->get($className);
    }

    /**
     * @param $interfaceName
     * @return mixed
     */
    protected function fetchInterface($interfaceName)
    {
        /** @var FileDescriptor $file */
        $file = $this->projectDescriptor->getFiles()->get($this->filename);

        return $file->getInterfaces()->get($interfaceName);
    }

    /**
     * @return mixed
     */
    protected function fetchTrait()
    {
        /** @var FileDescriptor $file */
        $file = $this->projectDescriptor->getFiles()->get($this->filename);

        return $file->getTraits()->get('\Luigi\Pizza\HasPrice');
    }

    /**
     * @param $name
     * @return ConstantDescriptor
     */
    protected function getClassConstantWithName($className, $name)
    {
        /** @var FileDescriptor $file */
        $file = $this->projectDescriptor->getFiles()->get($this->filename);
        $class = $file->getClasses()->get($className);

        return $class->getConstants()->get($name);
    }

    /**
     * @param $name
     * @return ConstantDescriptor
     */
    protected function getFileConstantWithName($name)
    {
        /** @var FileDescriptor $file */
        $file = $this->projectDescriptor->getFiles()->get($this->filename);

        return $file->getConstants()->get($name);
    }

    /**
     * @param $name
     * @return MethodDescriptor
     */
    protected function getMethodWithName($className, $name)
    {
        /** @var FileDescriptor $file */
        $file = $this->projectDescriptor->getFiles()->get($this->filename);
        $class = $file->getClasses()->get($className);

        return $class->getMethods()->get($name);
    }

    /**
     * @param $name
     * @return MethodDescriptor
     */
    protected function getMethodWithNameFromTrait($className, $name)
    {
        /** @var FileDescriptor $file */
        $file = $this->projectDescriptor->getFiles()->get($this->filename);
        $trait = $file->getTraits()->get($className);

        return $trait->getMethods()->get($name);
    }

    /**
     * @param $name
     * @return MethodDescriptor
     */
    protected function getMethodWithNameFromInterface($className, $name)
    {
        /** @var FileDescriptor $file */
        $file = $this->projectDescriptor->getFiles()->get($this->filename);
        $interface = $file->getInterfaces()->get($className);

        return $interface->getMethods()->get($name);
    }

    /**
     * @param $name
     * @return PropertyDescriptor
     */
    protected function getPropertyWithName($className, $name)
    {
        /** @var FileDescriptor $file */
        $file = $this->projectDescriptor->getFiles()->get($this->filename);
        $class = $file->getClasses()->get($className);

        return $class->getProperties()->get($name);
    }

    /**
     * @param string $name
     * @return PropertyDescriptor
     */
    protected function getPropertyWithNameFromTrait($className, $name)
    {
        /** @var FileDescriptor $file */
        $file = $this->projectDescriptor->getFiles()->get($this->filename);
        $trait = $file->getTraits()->get($className);

        return $trait->getProperties()->get($name);
    }
}
