<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.5
 *
 * @copyright 2010-2015 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */
namespace phpDocumentor\Reflection;


/**
 * Represents a file in the project.
 */
interface File
{
    /**
     * Returns the hash of the contents for this file.
     *
     * @return string
     */
    public function getHash();

    /**
     * Retrieves the contents of this file.
     *
     * @return string|null
     */
    public function getSource();

    /**
     * Returns the file path relative to the project's root.
     *
     * @return string
     */
    public function getPath();
}