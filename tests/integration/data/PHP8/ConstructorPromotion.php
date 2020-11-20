<?php

declare(strict_types=1);

namespace PHP8;

use DateTimeImmutable;

class ConstructorPromotion
{
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
        public string $name,
        protected string $email = 'test@example.com',
        private DateTimeImmutable $birth_date,
    ) {}
}
