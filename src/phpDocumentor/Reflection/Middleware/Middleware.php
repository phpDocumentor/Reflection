<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Middleware;

/**
 * Middleware can be uses to perform extra steps during the parsing process.
 */
interface Middleware
{
    /**
     * Executes this middle ware class.
     *
     * @param callable(Command): object $next
     */
    public function execute(Command $command, callable $next): object;
}
