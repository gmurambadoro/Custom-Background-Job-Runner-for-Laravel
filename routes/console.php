<?php

use App\Enum\PhpExecStatusEnum;
use App\Models\PhpExecCommandModel;
use App\Services\SimplePhpClassInvoker;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('runBackgroundJob', function () {
    while (true) { // todo: Have a progress bar
        $pendingCommands = PhpExecCommandModel::where('status', PhpExecStatusEnum::Pending->value)->paginate(20);

        if ($pendingCommands->isEmpty()) {
            break;
        }

        foreach ($pendingCommands as $command) {
            try {
                $command->update(['status' => PhpExecStatusEnum::Running->value]);

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
                    'output' => $exception->getTraceAsString(),
                ]);
            }
        }
    }
})
    ->purpose('Run PHP classes and methods in the background')
    ->everyMinute()
    ->withoutOverlapping();
