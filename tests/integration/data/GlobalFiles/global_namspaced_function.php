<?php
/**
 * setup.php - Document the file.
 *
 * File long description goes here.
 *
 * @author Test Sample
 */

namespace {
    /**
     * autoloader function.
     *
     * Function long description goes here.
     *
     * @param string $class Namespaced class name
     * @return void
     */
    function libAutoload($class){
        echo 'Do something';
    }
    spl_autoload_register('libAutoload');
}
