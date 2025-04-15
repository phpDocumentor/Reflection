<?php

declare(strict_types=1);

class AsyncPropertyPromotion
{
    public function __construct(
        protected(set) Pizza $pizza,
    ) {}
}
