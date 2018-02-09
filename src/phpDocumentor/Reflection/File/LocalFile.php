<?php
declare(strict_types=1);

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

namespace phpDocumentor\Reflection\File;

use phpDocumentor\Reflection\File;

/**
 * Represents a local file on the file system.
 */
final class LocalFile implements File
{
    /**
     * Path to the file.
     * @var string
     */
    private $path;

    /**
     * LocalFile constructor.
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * Returns the content of the file as a string.
     */
    public function getContents(): string
    {
        return file_get_contents($this->path);
    }

    /**
     * Returns md5 hash of the file.
     */
    public function md5(): string
    {
        return md5_file($this->path);
    }

    /**
     * Returns an relative path to the file.
     */
    public function path(): string
    {
        return $this->path;
    }
}
