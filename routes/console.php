<?php

use App\Enums\PhpExecStatusEnum;
use App\Models\PhpExecCommandModel;
use App\Services\SimplePhpClassInvoker;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

function runBackgroundJob(): void
{
    while (true) {
        $pendingCommands = PhpExecCommandModel::where('status', PhpExecStatusEnum::Pending->value)
            ->orderBy('priority', 'desc') // higher priority jobs first
            ->paginate(20);

        if ($pendingCommands->isEmpty()) {
            break;
        }

        foreach ($pendingCommands as $command) {
            try {
                $command->update(['status' => PhpExecStatusEnum::Running->value]);

                // todo: Check for delayed execution and run command after the delay

                SimplePhpClassInvoker::invoke(
                    fqcn: $command->fqcn,
                    method: $command->method,
                    static: $command->is_static,
                    arguments: $command->arguments ?? [],
                );

                $command->update(['status' => PhpExecStatusEnum::Completed->value]);

                Log::info(sprintf('Successfully executed command: %s::%s', $command->fqcn, $command->method));
            } catch (Throwable $exception) {
                Log::error($exception->getMessage());

                $command->update([
                    'status' => PhpExecStatusEnum::Failed->value,
                    'output' => collect([$exception->getMessage(), $exception->getTraceAsString()])->join(PHP_EOL),
                ]);
            }
        }
    }
}

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('runBackgroundJob', fn() => runBackgroundJob())
    ->purpose('Run PHP classes and methods in the background')
    ->everyMinute()
    ->withoutOverlapping();
