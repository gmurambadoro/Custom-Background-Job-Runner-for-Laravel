<?php

namespace App\Services;

use Throwable;

/**
 * Service responsible for invoking a PHP class method.
 *
 * This service provides a simple way to invoke methods on PHP classes, allowing for flexibility in terms of object instantiation and method invocation.
 */
final class SimplePhpClassInvoker
{
    /**
     * Invoke a method on a fully qualified class name (FQCN).
     *
     * @param string $fqcn Fully Qualified Class Name (FQCN) of the class to invoke the method on.
     * @param string $method Name of the method to be invoked.
     * @param bool $static Whether the method should be invoked statically on the FQCN or on an instance of the class.
     * @param array $arguments Arguments list, in the order expected by the method specified. Defaults to an empty array if not provided.
     *
     * @throws Throwable If an error occurs during method invocation.
     */
    public static function invoke(string $fqcn, string $method, bool $static, array $arguments = []): void
    {
        // todo: Validate arguments

        if ($static) {
            // Use dynamic property access to call the method statically on the FQCN
            $fqcn::{$method}(...$arguments);
        } else {
            // Create an instance of the class and invoke the method on it
            $instance = new $fqcn();
            call_user_func_array([$instance, $method], $arguments);
        }
    }
}
