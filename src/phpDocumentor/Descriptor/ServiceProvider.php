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
use Desarrolla2\Cache\Adapter\File;
use Desarrolla2\Cache\Cache;
use phpDocumentor\Descriptor\Builder\AssemblerFactory;
use phpDocumentor\Descriptor\Filter\ClassFactory;
use phpDocumentor\Descriptor\Filter\Filter;
use phpDocumentor\Descriptor\ProjectDescriptor\InitializerChain;
use phpDocumentor\Descriptor\ProjectDescriptor\InitializerCommand\DefaultFilters;
use phpDocumentor\Descriptor\ProjectDescriptor\InitializerCommand\DefaultValidators;
use phpDocumentor\Descriptor\ProjectDescriptor\InitializerCommand\ReflectionAssemblers;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator;

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
                $adapter = new File(sys_get_temp_dir());
                $cache = new Cache($adapter);

                return $cache;
            }
        );
    }

    /**
     * Adds the Building mechanism using the key 'descriptor.builder'.
     *
     * @param Application $app
     *
     * @return void
     */
    protected function addAnalyzer(Application $app)
    {
        $app['descriptor.analyzer'] = $app->share(
            function ($container) {
                $analyzer = new Analyzer(
                    $container['descriptor.builder.assembler.factory'],
                    $container['descriptor.filter'],
                    $container['validator'],
                    $container['descriptor.builder.initializers']
                );

                return $analyzer;
            }
        );
    }
}
