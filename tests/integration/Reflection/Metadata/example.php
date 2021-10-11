<?php

class myHookUsingClass
{
    public function test() {
        echo "Do something";
        hook('foo');
        echo "finish";
    }
}
