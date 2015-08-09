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

namespace phpDocumentor\Reflection\Php\Factory\File;

/**
 * Interface for Adapters uses by the File strategy class to interact with the file system.
 */
interface Adapter
{

    /**
     * Returns true when the file exists.
     *
     * @param string $filePath
     * @return boolean
     */
    public function fileExists($filePath);

    /**
     * Returns the content of the file as a string.
     *
     * @param $filePath
     * @return string
     */
    public function getContents($filePath);

    /**
     * Returns md5 hash of the file.
     *
     * @param $filePath
     * @return string
     */
    public function md5($filePath);

    /**
     * Returns an relative path to the file.
     *
     * @param string $filePath
     * @return string
     */
    public function path($filePath);
}