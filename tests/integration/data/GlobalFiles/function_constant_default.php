<?php

use Acme\Plugin;

use const Acme\FOO;

function foo( $output = Plugin::class ) {}

function bar( $output = OBJECT ) {}

function bar2( $output = FOO ) {}
