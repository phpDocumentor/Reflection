<?php

declare(strict_types=1);

namespace PhpParser;

use Composer\InstalledVersions;

use function strpos;

if (strpos(InstalledVersions::getVersion('nikic/php-parser') ?? '', '4') === 0) {
    /**
     * Modifiers used (as a bit mask) by various flags subnodes, for example on classes, functions,
     * properties and constants.
     */
    final class Modifiers
    {
        public const PUBLIC = 1;
        public const PROTECTED = 2;
        public const PRIVATE = 4;
        public const STATIC = 8;
        public const ABSTRACT = 16;
        public const FINAL = 32;
        public const READONLY = 64;
        public const PUBLIC_SET = 128;
        public const PROTECTED_SET = 256;
        public const PRIVATE_SET = 512;

        public const VISIBILITY_SET_MASK = self::PUBLIC_SET | self::PROTECTED_SET | self::PRIVATE_SET;
    }
}
