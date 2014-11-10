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

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\PropertyDescriptor;
use phpDocumentor\Descriptor\TraitDescriptor;
use phpDocumentor\Reflection\ClassReflector\MethodReflector;
use phpDocumentor\Reflection\ClassReflector\PropertyReflector;
use phpDocumentor\Reflection\TraitReflector;
use PhpParser\Node;
use PhpParser\Node\Stmt\Trait_;

/**
 * Assembles an TraitDescriptor using an TraitReflector.
 */
class TraitAssembler extends AssemblerAbstract
{
    /**
     * Creates a Descriptor from the provided data.
     *
     * @param Trait_ $data
     *
     * @return TraitDescriptor
     */
    public function create($data)
    {
        $traitDescriptor = new TraitDescriptor();

        $this->assembleDocBlock($data->docBlock, $traitDescriptor);

        $traitDescriptor->setFullyQualifiedStructuralElementName('\\' . $data->namespacedName->toString());
        $traitDescriptor->setName($data->name);
        $traitDescriptor->setLine($data->getLine());
        $traitDescriptor->setNamespace('\\' . $this->extractNamespace($data));

        $this->addMembers($data, $traitDescriptor);

        return $traitDescriptor;
    }

    private function addMembers(Trait_ $node, TraitDescriptor $traitDescriptor)
    {
        foreach ($node->stmts as $stmt) {
            switch (get_class($stmt)) {
                case 'PhpParser\Node\Stmt\TraitUse':
                    $this->addTraitUsesToDescriptor($traitDescriptor, $stmt);
                    break;
                case 'PhpParser\Node\Stmt\Property':
                    /** @var Node\Stmt\Property $stmt */
                    foreach ($stmt->props as $property) {
                        $this->addPropertyToDescriptor($traitDescriptor, $stmt, $property);
                    }
                    break;
                case 'PhpParser\Node\Stmt\ClassMethod':
                    $this->addMethodToDescriptor($traitDescriptor, $stmt);
                    break;
            }
        }
    }
}
