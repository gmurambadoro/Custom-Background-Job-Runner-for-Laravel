<?php

namespace App\Services;

use Throwable;

final class SimplePhpClassInvoker
{
    /**
     * @param string $fqcn Fully Classified Class Name (FQCN)
     * @param string $method Method to be invoked
     * @param bool $static Whether the method should be invoked statically on the FQCN or on the object instance
     * @param array $arguments Arguments list, in the order expected by the method specified
     * @throws Throwable
     */
    public static function invoke(string $fqcn, string $method, bool $static, array $arguments = []): void
    {
        // todo: Validate arguments

        if ($static) {
            $fqcn::{$method}(...$arguments);
        } else {
            // create an instance
            $instance = new $fqcn();
            call_user_func_array([$instance, $method], $arguments);
        }
    }
}
