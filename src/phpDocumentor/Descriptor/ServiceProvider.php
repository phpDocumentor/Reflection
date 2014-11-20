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

use Cilex\Application;
use Cilex\ServiceProviderInterface;
use phpDocumentor\Descriptor\Builder\AssemblerFactory;
use phpDocumentor\Descriptor\Builder\Reflector\ArgumentAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\ClassAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\ConstantAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\FileAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\FunctionAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\InterfaceAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\MethodAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\PropertyAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\AuthorAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\DeprecatedAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\ExampleAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\GenericTagAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\LinkAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\MethodAssembler as MethodTagAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\ParamAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\PropertyAssembler as PropertyTagAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\ReturnAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\SeeAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\SinceAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\ThrowsAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\TypeCollectionAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\UsesAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\VarAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\VersionAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\TraitAssembler;
use phpDocumentor\Descriptor\Filter\ClassFactory;
use phpDocumentor\Descriptor\Filter\Filter;
use phpDocumentor\Descriptor\Filter\StripIgnore;
use phpDocumentor\Descriptor\Filter\StripInternal;
use phpDocumentor\Descriptor\Filter\StripOnVisibility;
use phpDocumentor\Descriptor\ProjectDescriptor\InitializerChain;
use phpDocumentor\Descriptor\ProjectDescriptor\InitializerCommand\DefaultFilters;
use phpDocumentor\Descriptor\ProjectDescriptor\InitializerCommand\DefaultValidators;
use phpDocumentor\Descriptor\ProjectDescriptor\InitializerCommand\ReflectionAssemblers;
use phpDocumentor\Reflection\ClassReflector\ConstantReflector as ClassConstant;
use phpDocumentor\Reflection\ClassReflector;
use phpDocumentor\Reflection\ConstantReflector;
use phpDocumentor\Reflection\DocBlock\Tag\AuthorTag;
use phpDocumentor\Reflection\DocBlock\Tag\DeprecatedTag;
use phpDocumentor\Reflection\DocBlock\Tag\ExampleTag;
use phpDocumentor\Reflection\DocBlock\Tag\LinkTag;
use phpDocumentor\Reflection\DocBlock\Tag\MethodTag;
use phpDocumentor\Reflection\DocBlock\Tag\ParamTag;
use phpDocumentor\Reflection\DocBlock\Tag\PropertyTag;
use phpDocumentor\Reflection\DocBlock\Tag\ReturnTag;
use phpDocumentor\Reflection\DocBlock\Tag\SeeTag;
use phpDocumentor\Reflection\DocBlock\Tag\SinceTag;
use phpDocumentor\Reflection\DocBlock\Tag\ThrowsTag;
use phpDocumentor\Reflection\DocBlock\Tag\UsesTag;
use phpDocumentor\Reflection\DocBlock\Tag\VarTag;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Type\Collection as TypeCollection;
use phpDocumentor\Reflection\FileReflector;
use phpDocumentor\Reflection\FunctionReflector;
use phpDocumentor\Reflection\InterfaceReflector;
use phpDocumentor\Reflection\TraitReflector;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator;
use Zend\Cache\Storage\Adapter\Filesystem;
use Zend\Cache\Storage\Plugin\Serializer as SerializerPlugin;
use Zend\Cache\Storage\Plugin\PluginOptions;
use Zend\Cache\Storage\Plugin\Serializer;

/**
 * This provider is responsible for registering the Descriptor component with the given Application.
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * Adds the services needed to build the descriptors.
     *
     * @param Application $app An Application instance
     *
     * @return void
     */
    public function register(Application $app)
    {
        $app['parser.example.finder'] = new Example\Finder();

        $app['descriptor.builder.initializers'] = $app->share(
            function () use ($app) {
                $initializerChain = new InitializerChain();
                $initializerChain->addInitializer(new DefaultFilters());
                $initializerChain->addInitializer(new ReflectionAssemblers($app['parser.example.finder']));
                $initializerChain->addInitializer(new DefaultValidators($app['validator']));

                return $initializerChain;
            }
        );

        $app['descriptor.builder.assembler.factory'] = $app->share(
            function () use ($app) {
                return new AssemblerFactory();
            }
        );

        $app['descriptor.filter'] = $app->share(
            function () {
                return new Filter(new ClassFactory());
            }
        );

        $this->addCache($app);
        $this->addAnalyzer($app);

        $app['descriptor.project.analyzer'] = function () {
            return new ProjectAnalyzer();
        };
    }

    /**
     * Adds the caching mechanism to the dependency injection container with key 'descriptor.cache'.
     *
     * @param Application $app
     *
     * @return void
     */
    protected function addCache(Application $app)
    {
        $app['descriptor.cache'] = $app->share(
            function () {
                $cache = new Filesystem();
                $cache->setOptions(
                    array(
                        'namespace' => 'phpdoc-cache',
                        'cache_dir' => sys_get_temp_dir(),
                    )
                );
                $plugin = new Serializer();

                if (extension_loaded('igbinary')) {
                    $options = new PluginOptions();
                    $options->setSerializer('igbinary');

                    $plugin->setOptions($options);
                }

                $cache->addPlugin($plugin);

                return $cache;
            }
        );
    }

    /**
     * Adds the Building mechanism using the key 'descriptor.builder'.
     *
     * Please note that the type of serializer can be configured using the parameter 'descriptor.builder.serializer'; it
     * accepts any parameter that Zend\Serializer supports.
     *
     * @param Application $app
     *
     * @return void
     */
    protected function addAnalyzer(Application $app)
    {
        if (extension_loaded('igbinary')) {
            $app['descriptor.builder.serializer'] = 'IgBinary';
        } else {
            $app['descriptor.builder.serializer'] = 'PhpSerialize';
        }

        $app['descriptor.analyzer'] = $app->share(
            function ($container) {
                $analyzer = new Analyzer(
                    $container['descriptor.builder.assembler.factory'],
                    $container['descriptor.filter'],
                    $container['validator'],
                    $container['descriptor.builder.initializers']
                );

                if (isset($container['kernel.stopwatch'])) {
                    $analyzer->setStopWatch($container['kernel.stopwatch']);
                }

                return $analyzer;
            }
        );
    }
}
