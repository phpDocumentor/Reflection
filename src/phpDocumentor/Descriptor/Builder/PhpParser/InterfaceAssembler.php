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

namespace phpDocumentor\Descriptor\Builder\PhpParser;

use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Reflection\ClassReflector\MethodReflector;
use phpDocumentor\Reflection\ConstantReflector;
use phpDocumentor\Reflection\InterfaceReflector;
use PhpParser\Node;
use PhpParser\Node\Stmt\Interface_;

/**
 * Assembles an InterfaceDescriptor using an InterfaceReflector.
 */
class InterfaceAssembler extends AssemblerAbstract
{
    /**
     * Creates a Descriptor from the provided data.
     *
     * @param Interface_ $data
     *
     * @return InterfaceDescriptor
     */
    public function create($data)
    {
        $interfaceDescriptor = new InterfaceDescriptor();

        $this->assembleDocBlock($data->docBlock, $interfaceDescriptor);

        $interfaceDescriptor->setFullyQualifiedStructuralElementName('\\' . $data->namespacedName->toString());
        $interfaceDescriptor->setName($data->name);
        $interfaceDescriptor->setLine($data->getLine());
//        $interfaceDescriptor->setPackage($this->extractPackageFromDocBlock($data->getDocBlock()) ?: '');

        $interfaceDescriptor->setNamespace('\\' . $this->extractNamespace($data));

        foreach ($data->extends as $interfaceClassName) {
            $interfaceFqcn = '\\' . $interfaceClassName->toString();
            $interfaceDescriptor->getParent()->set($interfaceFqcn, $interfaceFqcn);
        }

        $this->addMethodsAndConstants($data, $interfaceDescriptor);

        return $interfaceDescriptor;
    }

    private function addMethodsAndConstants(Interface_ $node, InterfaceDescriptor $interfaceDescriptor)
    {
        foreach ($node->stmts as $stmt) {
            switch (get_class($stmt)) {
                case 'PhpParser\Node\Stmt\ClassMethod':
                    $this->addMethodToDescriptor($interfaceDescriptor, $stmt);
                    break;
                case 'PhpParser\Node\Stmt\ClassConst':
                    /** @var Node\Stmt\ClassConst $stmt */
                    foreach ($stmt->consts as $constant) {
                        $this->addClassConstantToDescriptor($interfaceDescriptor, $constant);
                    }
                    break;
            }
        }
    }
}
