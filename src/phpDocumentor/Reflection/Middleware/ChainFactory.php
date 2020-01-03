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

use InvalidArgumentException;
use function array_pop;
use function get_class;
use function gettype;
use function is_object;
use function sprintf;

final class ChainFactory
{
    /**
     * @param Middleware[] $middlewareList
     */
    public static function createExecutionChain(array $middlewareList, callable $lastCallable) : callable
    {
        while ($middleware = array_pop($middlewareList)) {
            if (!$middleware instanceof Middleware) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Middleware must be an instance of %s but %s was given',
                        Middleware::class,
                        is_object($middleware) ? get_class($middleware) : gettype($middleware)
                    )
                );
            }

            $lastCallable = static function ($command) use ($middleware, $lastCallable) {
                return $middleware->execute($command, $lastCallable);
            };
        }

        return $lastCallable;
    }
}
