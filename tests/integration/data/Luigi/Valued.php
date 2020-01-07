<?php
// phpcs:ignoreFile
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2018 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace Luigi;

/**
 * Any class implementing this interface has an associated price.
 *
 * Using this interface we can easily add the price of all components in a pizza by checking for this interface and
 * adding the prices together for all components.
 */
interface Valued
{
    const BASE_PRICE = 1;

    function getPrice();
}
