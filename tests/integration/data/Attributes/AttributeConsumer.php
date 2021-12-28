<?php

declare(strict_types=1);

/** Class docblock */
#[MyClassAttribute(4)]
#[MyClassAttribute2("FirstValue", 2)]
class AttributeConsumer
{
    /** @return void */
    #[MethodAttribute]
    public function docblockBefore(): void
    {
    }

    /** @return void */
    #[MethodAttribute]
    public function docblockAfter(): void
    {
    }
}
