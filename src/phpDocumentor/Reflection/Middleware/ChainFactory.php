<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Middleware;

use InvalidArgumentException;

final class ChainFactory
{
    /**
     * @param Middleware[] $middlewareList
     * @param callable $lastCallable
     */
    public static function createExecutionChain(array $middlewareList, callable $lastCallable): callable
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
