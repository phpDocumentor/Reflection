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

namespace phpDocumentor\Reflection\Php;

use phpDocumentor\Reflection\Exception;
use phpDocumentor\Reflection\Metadata\Metadata;

use function array_key_exists;
use function sprintf;

/** @internal This class is not part of the backwards compatibility promise */
trait MetadataContainer
{
    /** @var Metadata[] */
    private $metadata = [];

    /**
     * @throws Exception When metadata key already exists.
     */
    public function addMetadata(Metadata $metadata): void
    {
        if (array_key_exists($metadata->key(), $this->metadata)) {
            throw new Exception(sprintf('Metadata with key "%s" already exists', $metadata->key()));
        }

        $this->metadata[$metadata->key()] = $metadata;
    }

    /** @return Metadata[] */
    public function getMetadata(): array
    {
        return $this->metadata;
    }
}
