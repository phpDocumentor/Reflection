<?php
/**
 * Polder Knowledge / Reflection (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/Reflection for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
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
