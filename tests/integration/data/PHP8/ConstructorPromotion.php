<?php

declare(strict_types=1);

namespace PHP8;

use DateTimeImmutable;

class ConstructorPromotion
{
    private const DEFAULT_VALUE = 'default';

    /**
     * Constructor with promoted properties
     *
     * @param string $name my docblock name
     */
    public function __construct(
        /**
         * Summary
         *
         * Description
         *
         * @var string $name property description
         */
        public string $name = 'default name',
        protected Email $email = new Email(),
        private DateTimeImmutable $birth_date,
        private DateTimeImmutable $created_at = new DateTimeImmutable('now'),
        private array $uses_constants = [self::DEFAULT_VALUE],
    ) {}
}
