<?php

use App\Enums\PhpJobStatusEnum;
use App\Models\PhpExecCommandModel;
use App\Services\SimplePhpClassInvoker;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

function runBackgroundJob(): void
{
    while (true) {
        $pendingCommands = PhpExecCommandModel::where('status', PhpJobStatusEnum::Pending->value)
            ->orderBy('priority', 'desc') // higher priority jobs first
            ->paginate(20);

        if ($pendingCommands->isEmpty()) {
            break;
        }

        foreach ($pendingCommands as $command) {
            try {
                $command->update(['status' => PhpJobStatusEnum::Running->value]);

                // todo: Check for delayed execution and run command after the delay

                SimplePhpClassInvoker::invoke(
                    fqcn: $command->fqcn,
                    method: $command->method,
                    static: $command->is_static,
                    arguments: $command->arguments ?? [],
                );

                $command->update(['status' => PhpJobStatusEnum::Completed->value]);

                Log::info(sprintf('Successfully executed command: %s', $command->command_text));
            } catch (Throwable $exception) {
                Log::error(collect([$exception->getMessage(), $exception->getTraceAsString()])->join(PHP_EOL));

                $command->update([
                    'status' => PhpJobStatusEnum::Failed->value,
                    'output' => $exception->getMessage(),
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
