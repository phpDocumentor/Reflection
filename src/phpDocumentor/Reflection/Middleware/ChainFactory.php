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

namespace phpDocumentor\Reflection\Middleware;

use InvalidArgumentException;

final class ChainFactory
{
    /**
     * @param Middleware[] $middlewareList
     *
     * @param callable $lastCallable
     * @return callable
     */
    public static function createExecutionChain($middlewareList, callable $lastCallable)
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
            $lastCallable = function ($command) use ($middleware, $lastCallable) {
                return $middleware->execute($command, $lastCallable);
            };
        }
        return $lastCallable;
    }
}
