<?php

namespace App\Services;

final class SimplePhpClassInvoker
{
    public static function runBackgroundJob(string $fqcn, string $method, bool $static, array $arguments = []): void
    {
        if ($static) {
            $result = $fqcn::{$method}(...$arguments);
        } else {
            $result = $fqcn->{$method}(...$arguments);
        }

        dump($result);
    }
}
