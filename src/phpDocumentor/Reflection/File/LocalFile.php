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
use Override;
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
     */
    private readonly string $path;

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
    #[Override]
    public function getContents(): string
    {
        return (string) file_get_contents($this->path);
    }

    /**
     * Returns md5 hash of the file.
     */
    #[Override]
    public function md5(): string
    {
        return md5_file($this->path);
    }

    /**
     * Returns a relative path to the file.
     */
    #[Override]
    public function path(): string
    {
        return $this->path;
    }
}
