<?php

namespace App\Services;

use Illuminate\Console\Application;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use InvalidArgumentException;
use Throwable;

/**
 * Service responsible for invoking a PHP class method.
 *
 * This service provides a simple way to invoke methods on PHP classes, allowing for flexibility in terms of object instantiation and method invocation.
 */
final class SimplePhpClassInvoker
{
    /**
     * Get a collection of classes that are blacklisted.
     *
     * These classes are considered restricted and should not be used
     * in certain contexts (e.g., for security or stability reasons).
     * The collection includes Laravel's Artisan class, the application
     * classes, and Symfony's console application class.
     *
     * @return Collection
     */
    public static function getBlacklistedClasses(): Collection
    {
        return collect([
            \Artisan::class,
            Artisan::class,
            Application::class,
            \Symfony\Component\Console\Application::class,
            \Illuminate\Foundation\Application::class,
        ]);
    }

    /**
     * Get a collection of methods that are blacklisted.
     *
     * These methods are considered sensitive or potentially harmful
     * when invoked dynamically. For example, methods like "update",
     * "delete", and "destroy" can modify or remove critical data.
     *
     * @return Collection
     */
    public static function getBlacklistedMethods(): Collection
    {
        return collect([
            "update",
            "delete",
            "unlink",
            "save",
            "create",
            'destroy',
        ]);
    }

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
        if (self::getBlacklistedClasses()->map(fn(string $x) => str($x)->trim()->lower())->contains(str($fqcn)->trim()->lower()->toString())) {
            throw new InvalidArgumentException(sprintf("Class is not allowed %s::", $fqcn));
        }

        if (self::getBlacklistedMethods()->map(fn(string $x) => str($x)->lower()->trim()->toString())->contains(str($method)->trim()->lower()->toString())) {
            throw new InvalidArgumentException(sprintf("Method %s::%s is not allowed", $fqcn, $method));
        }

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
