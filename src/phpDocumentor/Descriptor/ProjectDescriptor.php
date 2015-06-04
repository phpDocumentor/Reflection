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

use phpDocumentor\Descriptor\ProjectDescriptor\Settings;
use phpDocumentor\Reflection\Fqsen;
use Traversable;

/**
 * Represents the entire project with its files, namespaces and indexes.
 */
final class ProjectDescriptor implements Interfaces\ProjectInterface, \IteratorAggregate
{
    /** @var string $name */
    private $name = '';

    /** @var Namespace_ $namespace */
    private $namespace;

    /** @var Settings $settings */
    private $settings;

    /** @var Collection[] */
    private $data = array();

    /** @var Collection $indexes */
    private $indexes;

    /**
     * Initializes this descriptor.
     *
     * @param string $name Name of the current project.
     */
    public function __construct($name)
    {
        $this->setName($name);
        $this->setSettings(new Settings());

        $namespace = new Namespace_(new Fqsen('\\'));
        $this->setNamespace($namespace);

        $this->setFiles(new Collection());
        $this->setIndexes(new Collection());

        $this->setPartials(new Collection());
    }

    /**
     * Sets the name for this project.
     *
     * @param string $name
     *
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the name of this project.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets all files on this project.
     *
     * @param Collection $files
     *
     * @return void
     */
    public function setFiles(Collection $files)
    {
        $this->set('files', $files);
    }

    /**
     * Returns all files with their sub-elements.
     *
     * @return Collection<File>
     */
    public function getFiles()
    {
        return $this->get('files');
    }

    /**
     * Sets all indexes for this project.
     *
     * An index is a compilation of references to elements, usually constructed in a compiler step, that aids template
     * generation by providing a conveniently assembled list. An example of such an index is the 'marker' index where
     * a list of TODOs and FIXMEs are located in a central location for reporting.
     *
     * @param Collection $indexes
     *
     * @return void
     */
    public function setIndexes(Collection $indexes)
    {
        $this->indexes = $indexes;
    }

    /**
     * Returns all indexes in this project.
     *
     * @see setIndexes() for more information on what indexes are.
     *
     * @return Collection
     */
    public function getIndexes()
    {
        return $this->indexes;
    }

    /**
     * Sets the root namespace for this project together with all sub-namespaces.
     *
     * @param Namespace_ $namespace
     *
     * @return void
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * Returns the root (global) namespace.
     *
     * @return Namespace_
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Sets the settings used to build the documentation for this project.
     *
     * @param Settings $settings
     *
     * @return void
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;
    }

    /**
     * Returns the settings used to build the documentation for this project.
     *
     * @return Settings
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Sets all partials that can be used in a template.
     *
     * Partials are blocks of text that can be inserted anywhere in a template using a special indicator. An example is
     * the introduction partial that can add a custom piece of text to the homepage.
     *
     * @param Collection $partials
     *
     * @return void
     */
    public function setPartials(Collection $partials)
    {
        $this->set('partials', $partials);
    }

    /**
     * Returns a list of all partials.
     *
     * @see setPartials() for more information on partials.
     *
     * @return Collection
     */
    public function getPartials()
    {
        return $this->get('partials');
    }

    /**
     * Checks whether the Project supports the given visibility.
     *
     * @param integer $visibility One of the VISIBILITY_* constants of the Settings class.
     *
     * @see Settings for a list of the available VISIBILITY_* constants.
     *
     * @return boolean
     */
    public function isVisibilityAllowed($visibility)
    {
        $visibilityAllowed = $this->getSettings()
            ? $this->getSettings()->getVisibility()
            : Settings::VISIBILITY_DEFAULT;

        return (bool) ($visibilityAllowed & $visibility);
    }

    /**
     * Retrieves data that can be cached such as all the structure of all files or documents.
     *
     * @return Collection
     */
    public function get($index)
    {
        if (! isset($this->data[$index])) {
            $this->data[$index] = new Collection();
        }

        return $this->data[$index];
    }

    /**
     * Sets a piece of data, such as a listing of files, that can be cached and retrieved later.
     *
     * @param string     $index
     * @param Collection $collection
     *
     * @return void
     */
    public function set($index, Collection $collection)
    {
        $this->data[$index] = $collection;
    }

    /**
     * Provides a magic interface to get and set cachable data.
     *
     * If the method name starts with 'get' than the requested cached data is returned using the provided index name or
     * null if it doesn't exist.
     * If the method name starts with 'set' than the cached data is set using the given index and data.
     *
     * @param string $name
     * @param array  $arguments {
     *   @element string      'name'
     *   @element Collection? 'data'
     * }
     *
     * @return Collection|null|void
     */
    public function __call($name, $arguments)
    {
        switch (substr($name, 3)) {
            case 'get':
                return $this->get(strtolower($arguments[0]));
                break;
            case 'set':
                $this->set(strtolower($arguments[0]), $arguments[1]);
                break;
        }
    }

    /**
     * Returns an iterator with which you can loop through the data that can be cached.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }
}
