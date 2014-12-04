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

namespace phpDocumentor\Descriptor\Cache;

use Desarrolla2\Cache\Adapter\AdapterInterface;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor\Settings;
use phpDocumentor\Fileset\Collection;

/**
 * Maps a projectDescriptor to and from a cache instance.
 */
class ProjectDescriptorMapper
{
    const KEY_SETTINGS = 'settings';
    const KEY_FILES    = 'files';

    /** @var AdapterInterface */
    protected $cache;

    /**
     * Initializes this mapper with the given cache instance.
     *
     * @param AdapterInterface $cache
     */
    public function __construct(AdapterInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Returns the Cache instance for this Mapper.
     *
     * @return AdapterInterface
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Returns the Project Descriptor from the cache.
     *
     * @param ProjectDescriptor $projectDescriptor
     *
     * @return void
     */
    public function populate(ProjectDescriptor $projectDescriptor)
    {
        $settings = $this->getCache()->get(self::KEY_SETTINGS);
        if ($settings) {
            $projectDescriptor->setSettings($settings);
        }

        $files = $this->getCache()->get(self::KEY_FILES);
        if ($files instanceof Collection) {
            $projectDescriptor->setFiles($files);
        }
    }

    /**
     * Stores a Project Descriptor in the Cache.
     *
     * @param ProjectDescriptor $projectDescriptor
     *
     * @return void
     */
    public function save(ProjectDescriptor $projectDescriptor)
    {
        $this->getCache()->set(self::KEY_SETTINGS, $projectDescriptor->getSettings());
        $this->getCache()->set(self::KEY_FILES, $projectDescriptor->getFiles());
    }
}
