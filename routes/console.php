<?php

use App\Enums\JobStatusEnum;
use App\Models\BackgroundJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

function runBackgroundJob(): void
{
    while (true) {
        $pendingJobs = BackgroundJob::where('status', JobStatusEnum::Pending->value)
            ->orderBy('priority', 'desc') // higher priority jobs first
            ->paginate(20);

        if ($pendingJobs->isEmpty()) {
            break;
        }

        foreach ($pendingJobs as $job) {
            try {
                $job->dispatch();

                Log::info(sprintf('Successfully executed command: %s', $job->refresh()->command_text));
            } catch (Throwable $exception) {
                Log::error(collect([$exception->getMessage(), $exception->getTraceAsString()])->join(PHP_EOL));

                $job->update([
                    'status' => JobStatusEnum::Failed->value,
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
