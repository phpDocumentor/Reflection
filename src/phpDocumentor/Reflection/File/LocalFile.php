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

namespace phpDocumentor\Reflection\File;

use InvalidArgumentException;
use phpDocumentor\Reflection\File;

use function file_exists;
use function file_get_contents;
use function md5_file;
use function sprintf;

/**
 * Represents a local file on the file system.
 */
final class LocalFile implements File
{
    /**
     * Path to the file.
     *
     * @var string
     */
    private $path;

    public function __construct(string $path)
    {
        if (!file_exists($path)) {
            throw new InvalidArgumentException(sprintf('File "%s" does not exist', $path));
        }

        $this->path = $path;
    }

    /**
     * Returns the content of the file as a string.
     */
    public function getContents(): string
    {
        return (string) file_get_contents($this->path);
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
