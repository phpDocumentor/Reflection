<?php declare(strict_types=1);

namespace Luigi;

const OVEN_TEMPERATURE = 9001;
define('\\Luigi\\MAX_OVEN_TEMPERATURE', 9002);
define('OUTSIDE_OVEN_TEMPERATURE', 9002);

function in_function_define(){
    define('IN_FUNCTION_OVEN_TEMPERATURE', 9003);
}
