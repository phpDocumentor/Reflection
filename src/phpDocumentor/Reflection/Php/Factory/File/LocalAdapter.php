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
 * Adapter to interact with local readable files.
 */
final class LocalAdapter implements Adapter
{

    /**
     * Returns true when the file exists.
     *
     * @param string $filePath
     * @return boolean
     */
    public function fileExists($filePath)
    {
        return file_exists($filePath);
    }

    /**
     * Returns the content of the file as a string.
     *
     * @param $filePath
     * @return string
     */
    public function getContents($filePath)
    {
        return file_get_contents($filePath);
    }

    /**
     * Returns md5 hash of the file.
     *
     * @param $filePath
     * @return string
     */
    public function md5($filePath)
    {
        return md5_file($filePath);
    }

    /**
     * Returns an relative path to the file.
     *
     * @param string $filePath
     * @return string
     */
    public function path($filePath)
    {
        return $filePath;
    }
}
