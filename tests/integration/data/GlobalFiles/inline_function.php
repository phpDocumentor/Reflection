<?php

class Foo {
    public function test() {
        function internal() {
            return "yep";
        }

        return internal();
    }
}
