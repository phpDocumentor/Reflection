<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php;

/** @api */
final class AsymmetricVisibility extends Visibility
{
    public function __construct(
        private Visibility $readVisibility,
        private Visibility $writeVisibility,
    ) {
        parent::__construct((string) $readVisibility);
    }

    public function getReadVisibility(): Visibility
    {
        return $this->readVisibility;
    }

    public function getWriteVisibility(): Visibility
    {
        return $this->writeVisibility;
    }
}
