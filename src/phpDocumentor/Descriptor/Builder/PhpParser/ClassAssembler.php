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

use phpDocumentor\Descriptor\Class_ as ClassDescriptor;
use phpDocumentor\Descriptor\Interface_;
use phpDocumentor\Reflection\Fqsen;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node;

/**
 * Assembles an Class_ using an Class_.
 */
class ClassAssembler extends AssemblerAbstract
{
    /**
     * Creates a Descriptor from the provided data.
     *
     * @param Class_ $data
     *
     * @return ClassDescriptor
     */
    public function create($data)
    {
        $classDescriptor = new ClassDescriptor(
            new Fqsen('\\' . $data->namespacedName->toString()),
            $data->docBlock,
            $data->extends ? new ClassDescriptor(new Fqsen('\\' . $data->extends)) : null,
            $data->isAbstract(),
            $data->isFinal()
        );

        foreach ($data->implements as $interfaceClassName) {
            $classDescriptor->addInterface(
                new Fqsen($interfaceFqcn = '\\' . $interfaceClassName->toString())
            );
        }

        $this->addMembers($data, $classDescriptor);

        return $classDescriptor;
    }

    private function addMembers(Class_ $node, ClassDescriptor $classDescriptor)
    {
        foreach ($node->stmts as $stmt) {
            switch (get_class($stmt)) {
                case 'PhpParser\Node\Stmt\TraitUse':
                    $this->addTraitUsesToDescriptor($classDescriptor, $stmt);
                    break;
                case 'PhpParser\Node\Stmt\Property':
                    /** @var Node\Stmt\Property $stmt */
                    foreach ($stmt->props as $property) {
                        // the $stmt is actually a collection of properties but is the one who has the DocBlock
                        if (! $property->getDocComment()) {
                            $comments = $property->getAttribute('comments');
                            $comments[] = $stmt->getDocComment();
                            $property->setAttribute('comments', $comments);
                        }

                        $this->addPropertyToDescriptor($classDescriptor, $stmt, $property);
                    }
                    break;
                case 'PhpParser\Node\Stmt\ClassMethod':
                    $this->addMethodToDescriptor($classDescriptor, $stmt);
                    break;
                case 'PhpParser\Node\Stmt\ClassConst':
                    /** @var Node\Stmt\ClassConst $stmt */
                    foreach ($stmt->consts as $constant) {
                        // the $stmt is actually a collection of constants but is the one who has the DocBlock
                        if (! $constant->getDocComment()) {
                            $comments = $constant->getAttribute('comments');
                            $comments[] = $stmt->getDocComment();
                            $constant->setAttribute('comments', $comments);
                        }

                        $this->addClassConstantToDescriptor($classDescriptor, $constant);
                    }
                    break;
            }
        }
    }
}
