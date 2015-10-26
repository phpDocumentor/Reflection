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


use phpDocumentor\Reflection\Php\File;

/**
 * Middleware specialized for the File strategy.
 * This kind of middleware allows you to add extra steps during the parse process of a project file.
 */
interface Middleware
{
    /**
     * Executes this middle ware class.
     * A middle ware class MUST return a File object or call the $next callable.
     *
     * @param CreateCommand $command
     * @param callable $next
     *
     * @return File
     */
    public function execute(CreateCommand $command, callable $next);
}
