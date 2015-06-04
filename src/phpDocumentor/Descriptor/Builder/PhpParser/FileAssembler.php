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
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\File;
use phpDocumentor\Descriptor\PackageDescriptor;
use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Reflection\ClassReflector;
use phpDocumentor\Reflection\ConstantReflector;
use phpDocumentor\Reflection\DocBlock\Context;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Event\PostDocBlockExtractionEvent;
use phpDocumentor\Reflection\Exception\UnreadableFile;
use phpDocumentor\Reflection\FileReflector;
use phpDocumentor\Reflection\FunctionReflector;
use phpDocumentor\Reflection\IncludeReflector;
use phpDocumentor\Reflection\InterfaceReflector;
use phpDocumentor\Reflection\PrettyPrinter;
use phpDocumentor\Reflection\TraitReflector;
use phpDocumentor\Reflection\Traverser;
use PhpParser\Comment\Doc;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Include_;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Const_;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\PropertyProperty;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\Node;
use PhpParser\NodeVisitor;
use Psr\Log\LogLevel;

/**
 * Assembles an File using an FileReflector and ParamDescriptors.
 */
final class FileAssembler extends AssemblerAbstract implements NodeVisitor
{
    const XDEBUG_MAX_NESTING_LEVEL = 10000;

    /** @var string */
    private $defaultPackageName = 'Default';

    /** @var File */
    private $fileDescriptor;

    /** @var Context */
    private $context;

    /** @var string */
    private $encoding = 'utf-8';

    /** @var string[] */
    private $markerTerms = array('FIXME', 'TODO');

    /** @var string */
    private $projectRoot = '';

    /**
     * Initializes XDebug with a higher nesting level.
     *
     * With big fluent interfaces it can happen that PHP-Parser's Traverser exceeds the 100 recursions limit; we set
     * it to 10000 to be sure.
     */
    public function __construct()
    {
        ini_set('xdebug.max_nesting_level', self::XDEBUG_MAX_NESTING_LEVEL);
    }

    /**
     * Changes the name of the default package.
     *
     * @param string $defaultPackageName
     *
     * @return void
     */
    public function setDefaultPackageName($defaultPackageName)
    {
        $this->defaultPackageName = $defaultPackageName;
    }

    /**
     * Registers what the expected encoding of the files in a project.
     *
     * The default is UTF-8, please note that changing the encoding will have a negative impact on performance because
     * all files need to be converted using iconv to utf-8.
     *
     * @param string $encoding
     *
     * @return void
     */
    public function setEncoding($encoding)
    {
        if (strtolower($encoding) !== 'utf-8' && !extension_loaded('iconv')) {
            throw new \InvalidArgumentException(
                'The iconv extension of PHP is required when dealing with an encoding other than UTF-8'
            );
        }

        $this->encoding = $encoding;
    }

    /**
     * Registers which 'markers' are to be collected from a given file.
     *
     * Markers are inline comments that start with a special keyword, such as `// TODO` and that may optionally be
     * followed by a colon. These markers are indexed by phpDocumentor and shown in a special report.
     *
     * @param string[] $markerTerms
     *
     * @return void
     */
    public function setMarkerTerms(array $markerTerms)
    {
        $this->markerTerms = $markerTerms;
    }

    /**
     * Registers the root folder for the files collected by this assembler.
     *
     * If you register the project root with this assembler than all files that are passed to this assembler will have
     * this part of the file's path removed. This mechanism ensures that it does not matter where you project is, the
     * file names will always be relative to the projct root.
     *
     * For example:
     *
     *   Suppose you have a file `/home/mvriel/myProject/index.php` that you want to parse then when you set the project
     *   root to `/home/mvriel/myProject` then the reflection library will only register `index.php` as the
     *   complete path.
     *
     * @param string $path
     *
     * @return void
     */
    public function setProjectRoot($path)
    {
        $this->projectRoot = $path;
    }

    /**
     * Creates a Descriptor from the provided data.
     *
     * @param \SplFileObject $data The contents of a file
     *
     * @return File
     */
    public function create($data)
    {
        $this->context = new Context();
        $contents = $this->getFileContents($data);

        $path = substr($data->getPathname(), strlen($this->projectRoot));
        $this->fileDescriptor = new File(md5($contents), $path, $contents);

        $this->createTraverser()->traverse($contents);

        return $this->fileDescriptor;
    }

    /**
     * Extracts the file DocBlock and register its member on the File.
     *
     * @param Node[] $nodes
     *
     * @return Node[]
     */
    public function beforeTraverse(array $nodes)
    {
//        $docblock = $this->extractFileDocBlock($nodes);
//
//        $this->assembleDocBlock($docblock, $this->fileDescriptor);
//
//        if ($docblock && class_exists('phpDocumentor\Event\Dispatcher')) {
//            Dispatcher::getInstance()->dispatch(
//                'reflection.docblock-extraction.post',
//                PostDocBlockExtractionEvent::createInstance($this)->setDocblock($docblock)
//            );
//        }
//
//        /** @var Collection $packages */
//        $packages = $this->fileDescriptor->getTags()->get('package', new Collection());
//
//        if (! $packages->offsetExists(0)) {
//            $tag = new TagDescriptor('package');
//            $tag->setDescription($this->defaultPackageName);
//            $packages->set(0, $tag);
//        }
//
        return $nodes;
    }

    /**
     * Registers the Namespace Aliases and Package on the File after they have been discovered.
     *
     * @param Node[] $nodes
     *
     * @return Node[]
     */
    public function afterTraverse(array $nodes)
    {
        $this->fileDescriptor->setNamespaceAliases(new Collection($this->context->getNamespaceAliases()));
    }

    /**
     * Executes any time a AST node is visited.
     *
     * This method is not used but required by the {@see NodeVisitor} interface.
     *
     * @param Node $node
     *
     * @return Node|null
     */
    public function enterNode(Node $node)
    {
        return $node;
    }

    /**
     * Registers all discovered children on this File and calls the Analyzer to construct all
     * child Descriptors.
     *
     * @param Node $node
     *
     * @return Node
     */
    public function leaveNode(Node $node)
    {
        $className = get_class($node);
        switch ($className) {
            case 'PhpParser\Node\Stmt\Use_':
                /** @var Node\Stmt\Use_ $node */
                foreach ($node->uses as $use) {
                    $this->context->setNamespaceAlias($use->alias, implode('\\', $use->name->parts));
                }
                break;
            case 'PhpParser\Node\Stmt\Namespace_':
                $namespace = isset($node->name) && ($node->name) ? implode('\\', $node->name->parts) : '';
                $this->context->setNamespace($namespace);
                break;
            case 'PhpParser\Node\Stmt\Class_':
                $this->createDescriptorFromNodeAndAddToCollection($node, $this->fileDescriptor->getClasses());
                break;
            case 'PhpParser\Node\Stmt\Trait_':
                $this->createDescriptorFromNodeAndAddToCollection($node, $this->fileDescriptor->getTraits());
                break;
            case 'PhpParser\Node\Stmt\Interface_':
                $this->createDescriptorFromNodeAndAddToCollection($node, $this->fileDescriptor->getInterfaces());
                break;
            case 'PhpParser\Node\Stmt\Function_':
                $this->createDescriptorFromNodeAndAddToCollection($node, $this->fileDescriptor);
                break;
            case 'PhpParser\Node\Stmt\Const_':
                /** @var \PhpParser\Node\Stmt\Const_ $node */
                foreach ($node->consts as $const) {
                    // the $node is actually a collection of constants but is the one who has the DocBlock
                    $comments = $const->getAttribute('comments');
                    $comments[] = $node->getDocComment();
                    $const->setAttribute('comments', $comments);
                    $this->createDescriptorFromNodeAndAddToCollection($const, $this->fileDescriptor->getConstants());
                }
                break;
            case 'PhpParser\Node\Expr\FuncCall':
                if ($this->isDefineFunctionCallWithBothArguments($node)) {
                    $this->createDescriptorFromNodeAndAddToCollection(
                        $this->createConstantNodeFromDefineFunction($node),
                        $this->fileDescriptor->getConstants()
                    );
                }
                break;
            case 'PhpParser\Node\Expr\Include_':
                //TODO: fix this.
                //$this->fileDescriptor->addInclude(new IncludeReflector($node, $this->context));
                break;
        }

        return $node;
    }

    /**
     * Scans the file for markers and stores them in the File.
     *
     * @param string         $fileContents
     * @param File $fileDescriptor
     *
     * @return void
     */
    private function scanForMarkers($fileContents, File $fileDescriptor)
    {
        $markerCollection = $fileDescriptor->getMarkers();

        foreach (explode("\n", $fileContents) as $lineNumber => $line) {
            preg_match_all(
                '~//[\s]*(' . implode('|', $this->markerTerms) . ')\:?[\s]*(.*)~',
                $line,
                $matches,
                PREG_SET_ORDER
            );

            foreach ($matches as $match) {
                $match[3] = $lineNumber + 1;

                list(,$type, $message, $line) = $match;

                $markerCollection->add(array('type' => $type, 'message' => $message, 'line' => $line));
            }

        }
    }

    /**
     * Extracts the file DocBlock from the given set of nodes and removes it so that it is not interpreted as Class
     * DocBlock.
     *
     * @param Node[] $nodes
     *
     * @return DocBlock|null
     */
    private function extractFileDocBlock(array &$nodes)
    {
        $docblock = null;

        /** @var Node $node */
        list($node, $key) = $this->getFirstNonHtmlNodeAndKey($nodes);

        if ($node) {
            $comments = (array)$node->getAttribute('comments');

            // remove non-DocBlock comments
            $comments = $this->removeAllNonDocBlockComments($comments);

            if (!empty($comments)) {
                $docblock = $this->findFileDocBlockAndRemoveFromCommentStack($comments, $node);
            }

            $nodes[$key] = $node;
        }

        return $docblock;
    }

    /**
     * Finds the first Node in the given series that is not an inline HTML Node (and thus a PHP Node).
     *
     * @param Node[] $nodes
     *
     * @return array Array with the discovered node and with the key (in that order); or null and 0 when none is found.
     */
    private function getFirstNonHtmlNodeAndKey(array $nodes)
    {
        $node = null;
        $key = 0;
        foreach ($nodes as $k => $n) {
            if (!$n instanceof Node\Stmt\InlineHTML) {
                $node = $n;
                $key = $k;
                break;
            }
        }

        return array($node, $key);
    }

    /**
     * From a given series of comments are all non-DocBlock comments removed to make it easier to search.
     *
     * @param Node[] $comments
     *
     * @return Doc[]
     */
    private function removeAllNonDocBlockComments($comments)
    {
        return array_values(
            array_filter(
                $comments,
                function ($comment) {
                    return $comment instanceof Doc;
                }
            )
        );
    }

    /**
     * Retrieves the first DocBlock from the given array of Comments and removes it from the comment stack so that it
     * will not be used again.
     *
     * the first DocBlock in a file documents the file if:
     *
     * - it precedes another DocBlock or
     * - it contains a @package tag and doesn't precede a class declaration or
     * - it precedes a non-documentable element (thus no include, require, class, function, define, const)
     *
     * @param Doc[] $comments
     * @param Node  $node
     *
     * @return DocBlock|null
     */
    private function findFileDocBlockAndRemoveFromCommentStack(array $comments, Node $node)
    {
        $docblock = null;
        try {
            $docblockNode = isset($comments[0]) ? $comments[0] : null;
            if ($docblockNode) {
                $docblock = new DocBlock((string)$docblockNode, null, new DocBlock\Location($docblockNode->getLine()));
            }

            if (! $this->isFileDocBlock($docblock, $node)) {
                return null;
            }

            // remove the file level DocBlock from the node's comments
            array_shift($comments);
        } catch (\Exception $e) {
//                    $this->log($e->getMessage(), LogLevel::CRITICAL);
        }

        // always update the comments attribute so that standard comments
        // do not stop DocBlock from being attached to an element
        $node->setAttribute('comments', $comments);

        return $docblock;
    }

    /**
     * @param DocBlock $docblock
     * @param Node     $fileNode
     * @return bool
     */
    private function isFileDocBlock(DocBlock $docblock, Node $fileNode)
    {
        return (!$fileNode instanceof Class_
            && !$fileNode instanceof Interface_
            && $docblock->hasTag('package'))
            || !$this->isNodeDocumentable($fileNode);
    }

    /**
     * Checks whether the given node is recogized by phpDocumentor as a
     * documentable element.
     *
     * The following elements are recognized:
     *
     * - Trait
     * - Class
     * - Interface
     * - Class constant
     * - Class method
     * - Property
     * - Include/Require
     * - Constant, both const and define
     * - Function
     *
     * @param Node $node
     *
     * @return bool
     */
    protected function isNodeDocumentable(Node $node)
    {
        return ($node instanceof Class_)
        || ($node instanceof Interface_)
        || ($node instanceof ClassConst)
        || ($node instanceof ClassMethod)
        || ($node instanceof Const_)
        || ($node instanceof Function_)
        || ($node instanceof Property)
        || ($node instanceof PropertyProperty)
        || ($node instanceof Trait_)
        || ($node instanceof Include_)
        || ($node instanceof FuncCall
            && ($node->name instanceof Name)
            && $node->name == 'define');
    }

    /**
     * Creates a new object that traverses the AST as discovered by PHP-Parser.
     *
     * @return Traverser
     */
    private function createTraverser()
    {
        $traverser = new Traverser();
        $traverser->addVisitor($this);

        return $traverser;
    }

    /**
     * Extracts the complete file source from an SplFileObject.
     *
     * @param \SplFileObject $data
     *
     * @return string
     */
    private function getFileContents($data)
    {
        $contents = '';
        foreach ($data as $line) {
            $contents .= $line;
        }

        $encoding = strtolower($this->encoding);
        if ($encoding !== 'utf-8') {
            $contents = iconv($encoding, 'utf-8//IGNORE//TRANSLIT', $contents);
        }

        return $contents;
    }

    /**
     * Verifies whether the given node is a `define` function call and whether the first and second argument is set.
     *
     * @param Node $node
     *
     * @return bool
     */
    private function isDefineFunctionCallWithBothArguments(Node $node)
    {
        return ($node->name instanceof Name)
            && ($node->name == 'define')
            && isset($node->args[0])
            && isset($node->args[1]);
    }

    /**
     * Creates a Node\Const_ object from a `define` function call so that they are valid constants and can be passed to
     * the Descriptor Analyzer.
     *
     * @param Node $node
     *
     * @return Node\Const_
     */
    private function createConstantNodeFromDefineFunction(Node $node)
    {
        $prettyPrinter = new PrettyPrinter();

        // transform the first argument of the define function call into a constant name
        $name = str_replace(
            array('\\\\', '"', "'"),
            array('\\', '', ''),
            trim($prettyPrinter->prettyPrintExpr($node->args[0]->value), '\'')
        );
        $nameParts = explode('\\', $name);
        $shortName = end($nameParts);

        $constant = new Node\Const_($shortName, $node->args[1]->value, $node->getAttributes());
        $constant->namespacedName = new Name($name);

        return $constant;
    }

    /**
     * Assembles a new Descriptor and if it is not filtered away it is added to the given collection.
     *
     * @param Node $node
     * @param Collection $collection
     *
     * @return void
     */
    private function createDescriptorFromNodeAndAddToCollection(Node $node, Collection $collection)
    {
        $node->docBlock = $node->getDocComment()
            ? new DocBlock($node->getDocComment()->getText(), $this->context)
            : null;

        /** @var DescriptorAbstract $descriptor */
        $descriptor = $this->getAnalyzer()->analyze($node);
        if (!$descriptor) {
            return;
        }

        if (method_exists($descriptor, 'setLocation')) {
            $descriptor->setLocation($this->fileDescriptor, $node->getLine());
        }

        if (method_exists($descriptor, 'getTags')) {
            $this->inheritPackageFromFileDescriptor($descriptor);
        }

        if (method_exists($descriptor, 'getFqsen')) {
            $collection->set((string)$descriptor->getFqsen(), $descriptor);
        } else {
            $collection->set($descriptor->getFullyQualifiedStructuralElementName(), $descriptor);
        }
    }

    /**
     * Takes the File's `@package` tag and applies it on the given Descriptor if it has no package of its own.
     *
     * @param DescriptorAbstract $descriptor
     *
     * @return void
     */
    private function inheritPackageFromFileDescriptor(DescriptorAbstract $descriptor)
    {
        if (count($descriptor->getTags()->get('package', new Collection())) == 0) {
            $descriptor->getTags()->set(
                'package',
                $this->fileDescriptor->getTags()->get('package', new Collection())
            );
        }
    }
}
