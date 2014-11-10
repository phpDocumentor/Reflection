<?php

namespace phpDocumentor\Descriptor\Builder\PhpParser;

use Mockery as m;
use org\bovigo\vfs\vfsStream;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\FunctionDescriptor;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\Analyzer;
use phpDocumentor\Descriptor\Tag\AuthorDescriptor;
use phpDocumentor\Descriptor\TraitDescriptor;
use phpDocumentor\Reflection\IncludeReflector;

class FileAssemblerTest extends \PHPUnit_Framework_TestCase
{
    const EXAMPLE = <<<PHP
<?php
/**
 * %s
 *
 * %s
 *
 * @%s %s
 */

namespace %s;

include "%s";

use %s;

const EXAMPLE_CONSTANT = 124;

define('EXAMPLE_CONSTANT2', 123);

function exampleFunction()
{
}

class Example
{
}

interface ExampleInterface
{
}

// TODO: %s
trait ExampleTrait
{
}
PHP;
    const EXAMPLE_SUMMARY          = 'This is a file DocBlock';
    const EXAMPLE_DESCRIPTION      = 'This is a file DocBlock Description';
    const EXAMPLE_TAG_NAME         = 'author';
    const EXAMPLE_TAG_DESCRIPTION  = 'Mike van Riel <me@mikevanriel.com>';
    const EXAMPLE_INCLUDE          = 'myInclude.php';
    const EXAMPLE_NAMESPACE        = 'My\\Space';
    const EXAMPLE_NAMESPACE_ALIAS  = 'Space as TheFinalFrontier';
    const EXAMPLE_CONSTANT_NAME    = 'EXAMPLECONSTANT';
    const EXAMPLE_DEFINE_NAME      = 'EXAMPLECONSTANT2';
    const EXAMPLE_FUNCTION_NAME    = 'ExampleFunction';
    const EXAMPLE_CLASS_NAME       = 'Example';
    const EXAMPLE_INTERFACE_NAME   = 'ExampleInterface';
    const EXAMPLE_TRAIT_NAME       = 'ExampleTrait';
    const EXAMPLE_CONSTANT_LINE    = 16;
    const EXAMPLE_FUNCTION_LINE    = 20;
    const EXAMPLE_CLASS_LINE       =  24;
    const EXAMPLE_INTERFACE_LINE   = 28;
    const EXAMPLE_TRAIT_LINE       = 33;
    const DEFAULT_PACKAGE_NAME     = 'Default';
    const EXAMPLE_TODO_MARKER      = 'Add a DocBlock';
    const EXAMPLE_TODO_MARKER_LINE = 32;

    /** @var FileAssembler */
    private $fixture;

    /** @var Analyzer|m\MockInterface */
    private $analyzerMock;

    /**
     * Initializes the fixture and its dependencies.
     */
    protected function setUp()
    {
        vfsStream::setup('tests');

        $this->analyzerMock = m::mock('phpDocumentor\Descriptor\Analyzer');

        $this->fixture = new FileAssembler();
        $this->fixture->setAnalyzer($this->analyzerMock);

        $this->thenAnAuthorTagShouldBeFound();
        $this->thenAConstantShouldBeAdded();
        $this->thenAConstantUsingDefineShouldBeAdded();
        $this->thenAFunctionShouldBeAdded();
        $this->thenAClassShouldBeAdded();
        $this->thenAnInterfaceShouldBeAdded();
        $this->thenATraitShouldBeAdded();
    }

    /**
     * @covers phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler::__construct
     * @covers phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler::create
     * @covers phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler::getFileContents
     * @covers phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler::createTraverser
     * @covers phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler::beforeTraverse
     * @covers phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler::afterTraverse
     * @covers phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler::leaveNode
     */
    public function testFilePropertiesAreSetWhenAssemblingADescriptor()
    {
        $exampleFile = $this->givenFileContents();
        $fileName    = $this->givenAFilename();
        $fileObject  = $this->givenAFileObjectWithContents($fileName, $exampleFile);

        $result = $this->fixture->create($fileObject);

        $this->assertInstanceOf('phpDocumentor\Descriptor\FileDescriptor', $result);
        $this->assertSame(md5($exampleFile), $result->getHash());
        $this->assertSame($exampleFile, $result->getSource());
        $this->assertSame(basename($fileName), $result->getName());
        $this->assertSame($fileName, $result->getPath());
    }

    /**
     * @covers phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler::create
     */
    public function testNamespaceAliasesAreSetWhenAssemblingADescriptor()
    {
        $exampleFile = $this->givenFileContents();
        $fileName    = $this->givenAFilename();
        $fileObject  = $this->givenAFileObjectWithContents($fileName, $exampleFile);

        $result = $this->fixture->create($fileObject);

        $this->assertSame(array('TheFinalFrontier' => '\Space'), $result->getNamespaceAliases()->getAll());
    }

    /**
     * @covers phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler::create
     */
    public function testNoNamespaceIsSetWhenAssemblingADescriptor()
    {
        $exampleFile = $this->givenFileContents();
        $fileName    = $this->givenAFilename();
        $fileObject  = $this->givenAFileObjectWithContents($fileName, $exampleFile);

        $result = $this->fixture->create($fileObject);

        $this->assertSame(null, $result->getNamespace()); // Files do not have a namespace!
    }

    /**
     * @covers phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler::create
     */
    public function testIncludeIsRegisteredWhenAssemblingADescriptor()
    {
        $exampleFile = $this->givenFileContents();
        $fileName    = $this->givenAFilename();
        $fileObject  = $this->givenAFileObjectWithContents($fileName, $exampleFile);

        $result = $this->fixture->create($fileObject);

        /** @var IncludeReflector $include */
        $include = current($result->getIncludes()->getAll());
        $this->assertInstanceOf('phpDocumentor\Reflection\IncludeReflector', $include);
        $this->assertSame(self::EXAMPLE_INCLUDE, $include->getShortName());
    }

    /**
     * @covers phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler::create
     * @covers phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler::extractFileDocBlock
     * @covers phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler::getFirstNonHtmlNodeAndKey
     * @covers phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler::removeAllNonDocBlockComments
     * @covers phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler::findFileDocBlockAndRemoveFromCommentStack
     * @covers phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler::isFileDocBlock
     * @covers phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler::isNodeDocumentable
     */
    public function testDocBlockIsCopiedWhenAssemblingADescriptor()
    {
        $exampleFile = $this->givenFileContents();
        $fileName    = $this->givenAFilename();
        $fileObject  = $this->givenAFileObjectWithContents($fileName, $exampleFile);

        $result = $this->fixture->create($fileObject);

        $this->assertSame(self::EXAMPLE_SUMMARY, $result->getSummary());
        $this->assertSame(self::EXAMPLE_DESCRIPTION, $result->getDescription());
        $this->assertSame(array(self::EXAMPLE_TAG_NAME, 'package'), array_keys($result->getTags()->getAll()));
        $this->assertCount(1, array_keys($result->getTags()->get('author')->getAll()));
        $this->assertInstanceOf(
            'phpDocumentor\Descriptor\Tag\AuthorDescriptor',
            current($result->getTags()->get('author')->getAll())
        );
        $this->assertSame(
            self::EXAMPLE_TAG_DESCRIPTION,
            current($result->getTags()->get('author')->getAll())->getDescription()
        );
    }

    /**
     * @covers phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler::create
     * @covers phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler::scanForMarkers
     */
    public function testMarkersAreCollectedWhenAssemblingADescriptor()
    {
        $exampleFile = $this->givenFileContents();
        $fileName    = $this->givenAFilename();
        $fileObject  = $this->givenAFileObjectWithContents($fileName, $exampleFile);

        $result = $this->fixture->create($fileObject);

        $this->assertCount(1, $result->getMarkers());
        $this->assertSame(
            array('type' => 'TODO', 'message' => self::EXAMPLE_TODO_MARKER, 'line' => self::EXAMPLE_TODO_MARKER_LINE),
            $result->getMarkers()->get(0)
        );
    }

    /**
     * @covers phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler::create
     */
    public function testPackageIsSetWhenAssemblingADescriptor()
    {
        $exampleFile = $this->givenFileContents();
        $fileName    = $this->givenAFilename();
        $fileObject  = $this->givenAFileObjectWithContents($fileName, $exampleFile);
        $result = $this->fixture->create($fileObject);

        $this->assertNull($result->getPackage());
        $this->assertInstanceOf('phpDocumentor\Descriptor\TagDescriptor', $result->getTags()->get('package')->get(0));
        $this->assertSame(self::DEFAULT_PACKAGE_NAME, $result->getTags()->get('package')->get(0)->getDescription());
    }

    /**
     * @covers phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler::create
     * @covers phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler::isDefineFunctionCallWithBothArguments
     * @covers phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler::createConstantNodeFromDefineFunction
     * @covers phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler::createDescriptorFromNodeAndAddToCollection
     * @covers phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler::inheritPackageFromFileDescriptor
     * @covers phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler::createDescriptorFromNodeAndAddToCollection
     * @covers phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler::inheritPackageFromFileDescriptor
     */
    public function testConstantsAreRegisteredWhenCreatingAFileDescriptor()
    {
        $exampleFile = $this->givenFileContents();
        $fileName    = $this->givenAFilename();
        $fileObject  = $this->givenAFileObjectWithContents($fileName, $exampleFile);

        $result = $this->fixture->create($fileObject);

        $fqcn = '\\' . self::EXAMPLE_NAMESPACE . '\\' . self::EXAMPLE_CONSTANT_NAME;
        $fqcn2 = '\\' . self::EXAMPLE_NAMESPACE . '\\' . self::EXAMPLE_DEFINE_NAME;
        $this->assertCount(2, $result->getConstants()->getAll());
        $this->assertInstanceOf('phpDocumentor\Descriptor\ConstantDescriptor', $result->getConstants()->get($fqcn));
        $this->assertInstanceOf('phpDocumentor\Descriptor\ConstantDescriptor', $result->getConstants()->get($fqcn2));
        $this->assertSame(
            $fqcn,
            $result->getConstants()->get($fqcn)->getFullyQualifiedStructuralElementName()
        );
        $this->assertSame(
            $fqcn2,
            $result->getConstants()->get($fqcn2)->getFullyQualifiedStructuralElementName()
        );
        $this->assertSame('\\' . self::EXAMPLE_NAMESPACE, current($result->getConstants()->getAll())->getNamespace());
        $this->assertSame($result, current($result->getConstants()->getAll())->getFile());
        $this->assertSame(self::EXAMPLE_CONSTANT_LINE, current($result->getConstants()->getAll())->getLine());
    }

    /**
     * @covers phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler::create
     * @covers phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler::createDescriptorFromNodeAndAddToCollection
     * @covers phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler::inheritPackageFromFileDescriptor
     */
    public function testFunctionsAreRegisteredWhenCreatingAFileDescriptor()
    {
        $exampleFile = $this->givenFileContents();
        $fileName    = $this->givenAFilename();
        $fileObject  = $this->givenAFileObjectWithContents($fileName, $exampleFile);

        $result = $this->fixture->create($fileObject);

        $fqcn = '\\' . self::EXAMPLE_NAMESPACE . '\\' . self::EXAMPLE_FUNCTION_NAME . '()';
        $this->assertCount(1, $result->getFunctions()->getAll());
        $this->assertInstanceOf('phpDocumentor\Descriptor\FunctionDescriptor', $result->getFunctions()->get($fqcn));
        $this->assertSame(
            $fqcn,
            $result->getFunctions()->get($fqcn)->getFullyQualifiedStructuralElementName()
        );
        $this->assertSame('\\' . self::EXAMPLE_NAMESPACE, current($result->getFunctions()->getAll())->getNamespace());
        $this->assertSame($result, current($result->getFunctions()->getAll())->getFile());
        $this->assertSame(self::EXAMPLE_FUNCTION_LINE, current($result->getFunctions()->getAll())->getLine());
    }

    /**
     * @covers phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler::create
     * @covers phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler::createDescriptorFromNodeAndAddToCollection
     * @covers phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler::inheritPackageFromFileDescriptor
     */
    public function testClassesAreRegisteredWhenCreatingAFileDescriptor()
    {
        $exampleFile = $this->givenFileContents();
        $fileName    = $this->givenAFilename();
        $fileObject  = $this->givenAFileObjectWithContents($fileName, $exampleFile);

        $result = $this->fixture->create($fileObject);

        $fqcn = '\\' . self::EXAMPLE_NAMESPACE . '\\' . self::EXAMPLE_CLASS_NAME;
        $this->assertCount(1, $result->getClasses()->getAll());
        $this->assertInstanceOf('phpDocumentor\Descriptor\ClassDescriptor', $result->getClasses()->get($fqcn));
        $this->assertSame(
            $fqcn,
            $result->getClasses()->get($fqcn)->getFullyQualifiedStructuralElementName()
        );
        $this->assertSame('\\' . self::EXAMPLE_NAMESPACE, current($result->getClasses()->getAll())->getNamespace());
        $this->assertSame($result, current($result->getClasses()->getAll())->getFile());
        $this->assertSame(self::EXAMPLE_CLASS_LINE, current($result->getClasses()->getAll())->getLine());
    }

    /**
     * @covers phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler::create
     * @covers phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler::createDescriptorFromNodeAndAddToCollection
     * @covers phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler::inheritPackageFromFileDescriptor
     */
    public function testTraitsAreRegisteredWhenCreatingAFileDescriptor()
    {
        $exampleFile = $this->givenFileContents();
        $fileName    = $this->givenAFilename();
        $fileObject  = $this->givenAFileObjectWithContents($fileName, $exampleFile);

        $result = $this->fixture->create($fileObject);

        $traitFqcn = '\\' . self::EXAMPLE_NAMESPACE . '\\' . self::EXAMPLE_TRAIT_NAME;
        $this->assertCount(1, $result->getTraits()->getAll());
        $this->assertInstanceOf('phpDocumentor\Descriptor\TraitDescriptor', $result->getTraits()->get($traitFqcn));
        $this->assertSame(
            $traitFqcn,
            $result->getTraits()->get($traitFqcn)->getFullyQualifiedStructuralElementName()
        );
        $this->assertSame('\\' . self::EXAMPLE_NAMESPACE, current($result->getTraits()->getAll())->getNamespace());
        $this->assertSame($result, current($result->getTraits()->getAll())->getFile());
        $this->assertSame(self::EXAMPLE_TRAIT_LINE, current($result->getTraits()->getAll())->getLine());
    }

    /**
     * @covers phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler::create
     * @covers phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler::createDescriptorFromNodeAndAddToCollection
     * @covers phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler::inheritPackageFromFileDescriptor
     */
    public function testInterfacesAreRegisteredWhenCreatingAFileDescriptor()
    {
        $exampleFile = $this->givenFileContents();
        $fileName    = $this->givenAFilename();
        $fileObject  = $this->givenAFileObjectWithContents($fileName, $exampleFile);

        $result = $this->fixture->create($fileObject);

        $fcqn = '\\' . self::EXAMPLE_NAMESPACE . '\\' . self::EXAMPLE_INTERFACE_NAME;
        $this->assertCount(1, $result->getInterfaces()->getAll());
        $this->assertInstanceOf(
            'phpDocumentor\Descriptor\InterfaceDescriptor',
            $result->getInterfaces()->get($fcqn)
        );
        $this->assertSame(
            $fcqn,
            $result->getInterfaces()->get($fcqn)->getFullyQualifiedStructuralElementName()
        );
        $this->assertSame('\\' . self::EXAMPLE_NAMESPACE, current($result->getInterfaces()->getAll())->getNamespace());
        $this->assertSame($result, current($result->getInterfaces()->getAll())->getFile());
        $this->assertSame(self::EXAMPLE_INTERFACE_LINE, current($result->getInterfaces()->getAll())->getLine());
    }

    /**
     * Returns a path where the example file can be found.
     *
     * We use vfsStream to create an example file so that we do not pollute the real filesystem and because an
     * in-memory representation is faster.
     *
     * @return string
     */
    private function givenAFilename()
    {
        return vfsStream::url('tests/mock.php');
    }

    /**
     * Creates an SplFileFileObject for the given path with the given contents.
     *
     * @param string $tmpFileName
     * @param string $exampleFileContents
     *
     * @return \SplFileObject
     */
    private function givenAFileObjectWithContents($tmpFileName, $exampleFileContents)
    {
        file_put_contents($tmpFileName, $exampleFileContents);

        return new \SplFileObject($tmpFileName);
    }

    /**
     * Returns the source of the example file used in these tests.
     *
     * @return string
     */
    private function givenFileContents()
    {
        return sprintf(
            self::EXAMPLE,
            self::EXAMPLE_SUMMARY,
            self::EXAMPLE_DESCRIPTION,
            self::EXAMPLE_TAG_NAME,
            self::EXAMPLE_TAG_DESCRIPTION,
            self::EXAMPLE_NAMESPACE,
            self::EXAMPLE_INCLUDE,
            self::EXAMPLE_NAMESPACE_ALIAS,
            self::EXAMPLE_TODO_MARKER
        );
    }

    /**
     * Instructs the mocks to expect that an author tag is built and populated with the right expects.
     *
     * @return void
     */
    private function thenAnAuthorTagShouldBeFound()
    {
        $authorDescriptor = new AuthorDescriptor(self::EXAMPLE_TAG_NAME);
        $authorDescriptor->setDescription(self::EXAMPLE_TAG_DESCRIPTION);

        $this->analyzerMock->shouldReceive('analyze')
            ->with(m::type('phpDocumentor\Reflection\DocBlock\Tag'))
            ->andReturn($authorDescriptor);
    }

    /**
     * Instructs the mocks to expect that a function is built and populated with the right expects.
     *
     * @return void
     */
    private function thenAFunctionShouldBeAdded()
    {
        $descriptor = new FunctionDescriptor();
        $descriptor->setFullyQualifiedStructuralElementName(
            '\\' . self::EXAMPLE_NAMESPACE . '\\' . self::EXAMPLE_FUNCTION_NAME . '()'
        );
        $descriptor->setNamespace('\\' . self::EXAMPLE_NAMESPACE);

        $this->analyzerMock->shouldReceive('analyze')
            ->once()
            ->with(m::type('PhpParser\Node\Stmt\Function_'))
            ->andReturn($descriptor);
    }

    /**
     * Instructs the mocks to expect that a constant is built and populated with the right expects.
     *
     * @return void
     */
    private function thenAConstantShouldBeAdded()
    {
        $descriptor = new ConstantDescriptor();
        $descriptor->setFullyQualifiedStructuralElementName(
            '\\' . self::EXAMPLE_NAMESPACE . '\\' . self::EXAMPLE_CONSTANT_NAME
        );
        $descriptor->setNamespace('\\' . self::EXAMPLE_NAMESPACE);

        $this->analyzerMock->shouldReceive('analyze')
            ->once()
            ->with(m::type('PhpParser\Node\Const_'))
            ->andReturn($descriptor);
    }

    /**
     * Instructs the mocks to expect that a constant using the define function is built and populated
     * with the right expects.
     *
     * @return void
     */
    private function thenAConstantUsingDefineShouldBeAdded()
    {
        $descriptor = new ConstantDescriptor();
        $descriptor->setFullyQualifiedStructuralElementName(
            '\\' . self::EXAMPLE_NAMESPACE . '\\' . self::EXAMPLE_DEFINE_NAME
        );
        $descriptor->setNamespace('\\' . self::EXAMPLE_NAMESPACE);

        $this->analyzerMock->shouldReceive('analyze')
            ->once()
            ->with(m::type('PhpParser\Node\Const_'))
            ->andReturn($descriptor);
    }

    /**
     * Instructs the mocks to expect that a class is built and populated with the right expects.
     *
     * @return void
     */
    private function thenAClassShouldBeAdded()
    {
        $descriptor = new ClassDescriptor();
        $descriptor->setFullyQualifiedStructuralElementName(
            '\\' . self::EXAMPLE_NAMESPACE . '\\' . self::EXAMPLE_CLASS_NAME
        );
        $descriptor->setNamespace('\\' . self::EXAMPLE_NAMESPACE);

        $this->analyzerMock->shouldReceive('analyze')
            ->once()
            ->with(m::type('PhpParser\Node\Stmt\Class_'))
            ->andReturn($descriptor);
    }

    /**
     * Instructs the mocks to expect that an interface is built and populated with the right expects.
     *
     * @return void
     */
    private function thenAnInterfaceShouldBeAdded()
    {
        $descriptor = new InterfaceDescriptor();
        $descriptor->setFullyQualifiedStructuralElementName(
            '\\' . self::EXAMPLE_NAMESPACE . '\\' . self::EXAMPLE_INTERFACE_NAME
        );
        $descriptor->setNamespace('\\' . self::EXAMPLE_NAMESPACE);

        $this->analyzerMock->shouldReceive('analyze')
            ->once()
            ->with(m::type('PhpParser\Node\Stmt\Interface_'))
            ->andReturn($descriptor);
    }

    /**
     * Instructs the mocks to expect that a trait is built and populated with the right expects.
     *
     * @return void
     */
    private function thenATraitShouldBeAdded()
    {
        $descriptor = new TraitDescriptor();
        $descriptor->setFullyQualifiedStructuralElementName(
            '\\' . self::EXAMPLE_NAMESPACE . '\\' . self::EXAMPLE_TRAIT_NAME
        );
        $descriptor->setNamespace('\\' . self::EXAMPLE_NAMESPACE);

        $this->analyzerMock->shouldReceive('analyze')
            ->once()
            ->with(m::type('PhpParser\Node\Stmt\Trait_'))
            ->andReturn($descriptor);
    }
}
