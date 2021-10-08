<?php

declare(strict_types=1);

namespace MyNamespace;

class EnumConsumer
{
    public MyEnum $myEnum = MyEnum::VALUE1;

    public function consume(MyEnum $enum = MyEnum::VALUE1): MyEnum
    {
        $this->myEnum = $enum;
    }
}
