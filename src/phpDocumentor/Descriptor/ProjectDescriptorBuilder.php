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

use phpDocumentor\Descriptor\Builder\AssemblerFactory;
use phpDocumentor\Descriptor\Builder\Reflector\AssemblerAbstract;
use phpDocumentor\Descriptor\Example\Finder;
use phpDocumentor\Descriptor\Filter\ClassFactory;
use phpDocumentor\Descriptor\Filter\Filter;
use phpDocumentor\Descriptor\Filter\Filterable;
use phpDocumentor\Descriptor\ProjectDescriptor\InitializerChain;
use phpDocumentor\Descriptor\ProjectDescriptor\InitializerCommand\DefaultFilters;
use phpDocumentor\Descriptor\ProjectDescriptor\InitializerCommand\DefaultValidators;
use phpDocumentor\Descriptor\ProjectDescriptor\InitializerCommand\PhpParserAssemblers;
use phpDocumentor\Descriptor\ProjectDescriptor\InitializerCommand\ReflectionAssemblers;
use phpDocumentor\Descriptor\ProjectDescriptor\Settings;
use phpDocumentor\Descriptor\Validator\Error;
use Psr\Log\LogLevel;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Builds a Project Descriptor and underlying tree.
 */
class ProjectDescriptorBuilder
{
    const OPTION_VALIDATOR = 'validator';
    const OPTION_EXAMPLE_FINDER = 'example.finder';
    const OPTION_INITIALIZERS = 'descriptor.builder.initializers';
    const OPTION_ASSEMBLER_FACTORY = 'descriptor.assembler.factory';
    const OPTION_DESCRIPTOR_FILTER = 'descriptor.filter';
    /** @var string */
    const DEFAULT_PROJECT_NAME = 'Untitled project';

    /** @var AssemblerFactory $assemblerFactory */
    protected $assemblerFactory;

    /** @var ValidatorInterface $validator */
    protected $validator;

    /** @var Filter $filter */
    protected $filter;

    /** @var ProjectDescriptor $project */
    protected $project;

    /** @var Stopwatch */
    private $stopwatch;

    public function __construct(
        AssemblerFactory $assemblerFactory,
        Filter $filterManager,
        $validator
    ) {
        $this->assemblerFactory = $assemblerFactory;
        $this->validator        = $validator;
        $this->filter           = $filterManager;
    }

    public static function create($options = array())
    {
        $validator = isset($options[self::OPTION_VALIDATOR])
            ? $options[self::OPTION_VALIDATOR]
            : Validation::createValidator();
        $exampleFinder = isset($options[self::OPTION_EXAMPLE_FINDER])
            ? $options[self::OPTION_EXAMPLE_FINDER]
            : new Finder();
        $assemblerFactory = isset($options[self::OPTION_ASSEMBLER_FACTORY])
            ? $options[self::OPTION_ASSEMBLER_FACTORY]
            : new AssemblerFactory();
        $filterManager = isset($options[self::OPTION_DESCRIPTOR_FILTER])
            ? $options[self::OPTION_DESCRIPTOR_FILTER]
            : new Filter(new ClassFactory());

        if (! isset($options[self::OPTION_INITIALIZERS])) {
            $initializerChain = new InitializerChain();
            $initializerChain->addInitializer(new DefaultFilters());
            $initializerChain->addInitializer(new PhpParserAssemblers($exampleFinder));
            $initializerChain->addInitializer(new ReflectionAssemblers($exampleFinder));
            $initializerChain->addInitializer(new DefaultValidators($validator));
        } else {
            $initializerChain = $options[self::OPTION_INITIALIZERS];
        }

        $builder = new static($assemblerFactory, $filterManager, $validator);

        $builder->createProjectDescriptor();
        $initializerChain->initialize($builder);

        return $builder;
    }

    public function createProjectDescriptor()
    {
        $this->project = new ProjectDescriptor(self::DEFAULT_PROJECT_NAME);
    }

    public function setProjectDescriptor(ProjectDescriptor $projectDescriptor)
    {
        $this->project = $projectDescriptor;
    }

    /**
     * Returns the project descriptor that is being built.
     *
     * @return ProjectDescriptor
     */
    public function getProjectDescriptor()
    {
        return $this->project;
    }

    /**
     * @return AssemblerFactory
     */
    public function getAssemblerFactory()
    {
        return $this->assemblerFactory;
    }

    /**
     * @return Filter
     */
    public function getFilterManager()
    {
        return $this->filter;
    }

    /**
     * @return ValidatorInterface
     */
    public function getValidator()
    {
        return $this->validator;
    }

    public function setStopWatch(Stopwatch $stopwatch)
    {
        $this->stopwatch = $stopwatch;
    }

    /**
     * Verifies whether the given visibility is allowed to be included in the Descriptors.
     *
     * This method is used anytime a Descriptor is added to a collection (for example, when adding a Method to a Class)
     * to determine whether the visibility of that element is matches what the user has specified when it ran
     * phpDocumentor.
     *
     * @param string|integer $visibility One of the visibility constants of the ProjectDescriptor class or the words
     *     'public', 'protected', 'private' or 'internal'.
     *
     * @see ProjectDescriptor where the visibility is stored and that declares the constants to use.
     *
     * @return boolean
     */
    public function isVisibilityAllowed($visibility)
    {
        switch ($visibility) {
            case 'public':
                $visibility = Settings::VISIBILITY_PUBLIC;
                break;
            case 'protected':
                $visibility = Settings::VISIBILITY_PROTECTED;
                break;
            case 'private':
                $visibility = Settings::VISIBILITY_PRIVATE;
                break;
            case 'internal':
                $visibility = Settings::VISIBILITY_INTERNAL;
                break;
        }

        return $this->getProjectDescriptor()->isVisibilityAllowed($visibility);
    }

    public function buildFileUsingSourceData($data)
    {
        if ($this->stopwatch) {
            $this->stopwatch->start($data->getFilename());
        }

        $descriptor = $this->buildDescriptor($data);
        if (!$descriptor) {
            return;
        }

        $this->getProjectDescriptor()->getFiles()->set($descriptor->getPath(), $descriptor);

        if ($this->stopwatch){
            $event = $this->stopwatch->stop($data->getFilename());
        }
    }

    /**
     * Takes the given data and attempts to build a Descriptor from it.
     *
     * @param mixed $data
     *
     * @throws \InvalidArgumentException if no Assembler could be found that matches the given data.
     *
     * @return DescriptorAbstract|Collection|null
     */
    public function buildDescriptor($data)
    {
        $assembler = $this->getAssembler($data);
        if (!$assembler) {
            throw new \InvalidArgumentException(
                'Unable to build a Descriptor; the provided data did not match any Assembler '. get_class($data)
            );
        }

        if ($assembler instanceof Builder\AssemblerAbstract) {
            $assembler->setBuilder($this);
        }

        // create Descriptor and populate with the provided data
        $descriptor = $assembler->create($data);
        if (!$descriptor) {
            return null;
        }

        $descriptor = (!is_array($descriptor) && (!$descriptor instanceof Collection))
            ? $this->filterAndValidateDescriptor($descriptor)
            : $this->filterAndValidateEachDescriptor($descriptor);

        return $descriptor;
    }

    /**
     * Attempts to find an assembler matching the given data.
     *
     * @param mixed $data
     *
     * @return AssemblerAbstract
     */
    public function getAssembler($data)
    {
        return $this->assemblerFactory->get($data);
    }

    /**
     * Analyzes a Descriptor and alters its state based on its state or even removes the descriptor.
     *
     * @param Filterable $descriptor
     *
     * @return Filterable
     */
    public function filter(Filterable $descriptor)
    {
        return $this->filter->filter($descriptor);
    }

    /**
     * Validates the contents of the Descriptor and outputs warnings and error if something is amiss.
     *
     * @param DescriptorAbstract $descriptor
     *
     * @return Collection
     */
    public function validate($descriptor)
    {
        $violations = $this->validator->validate($descriptor);
        $errors = new Collection();

        /** @var ConstraintViolation $violation */
        foreach ($violations as $violation) {
            $errors->add(
                new Error(
                    LogLevel::ERROR, // TODO: Make configurable
                    $violation->getMessageTemplate(),
                    $descriptor->getLine(),
                    $violation->getMessageParameters() + array($descriptor->getFullyQualifiedStructuralElementName())
                )
            );
        }

        return $errors;
    }

    /**
     * Filters each descriptor, validates them, stores the validation results and returns a collection of transmuted
     * objects.
     *
     * @param DescriptorAbstract[] $descriptor
     *
     * @return Collection
     */
    private function filterAndValidateEachDescriptor($descriptor)
    {
        $descriptors = new Collection();
        foreach ($descriptor as $key => $item) {
            $item = $this->filterAndValidateDescriptor($item);
            if (!$item) {
                continue;
            }

            $descriptors[$key] = $item;
        }

        return $descriptors;
    }

    /**
     * Filters a descriptor, validates it, stores the validation results and returns the transmuted object or null
     * if it is supposed to be removed.
     *
     * @param DescriptorAbstract $descriptor
     *
     * @return DescriptorAbstract|null
     */
    protected function filterAndValidateDescriptor($descriptor)
    {
        if (!$descriptor instanceof Filterable) {
            return $descriptor;
        }

        // filter the descriptor; this may result in the descriptor being removed!
        $descriptor = $this->filter($descriptor);
        if (!$descriptor) {
            return null;
        }

        // Validate the descriptor and store any errors
        $descriptor->setErrors($this->validate($descriptor));

        return $descriptor;
    }
}
