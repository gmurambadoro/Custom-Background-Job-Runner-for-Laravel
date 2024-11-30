<?php

use App\Enums\JobPriorityEnum;
use App\Models\BackgroundJob;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

function runBackgroundJob(): void
{
    $sampleJobsCollection = collect([
        BackgroundJob::make([
            'fqcn' => Artisan::class,
            'is_static' => true,
            'method' => 'command',
            'arguments' => ['app:php-exec'],
        ]),

        BackgroundJob::make([
            'fqcn' => User::class,
            'is_static' => true,
            'method' => 'find',
            'arguments' => [1],
        ]),

        BackgroundJob::make([
            'fqcn' => Process::class,
            'is_static' => true,
            'delay' => 20,
            'method' => 'run',
            'priority' => JobPriorityEnum::Medium->value,
            'arguments' => ['ls -lh'],
        ]),

        BackgroundJob::make([
            'fqcn' => Process::class,
            'is_static' => true,
            'delay' => 20,
            'method' => 'run',
            'priority' => JobPriorityEnum::High->value,
            'arguments' => ['cd /'],
        ]),
    ]);

    Log::channel('custom')->info(sprintf('Running a collection of %s sample background jobs', $sampleJobsCollection->count()));

    /** @var BackgroundJob $job */
    foreach ($sampleJobsCollection as $job) {
        Artisan::call(
            command: "app:php-exec",
            parameters: [
                'fqcn' => $job->fqcn,
                'method' => $job->method,
                'arguments' => $job->arguments,
                '--delay' => $job->delay,
                '--static' => $job->is_static,
                '--priority' => $job->priority->value,
            ],
        );
    }

    Log::channel('custom')->info(sprintf('Completed running a sample of %s background jobs.', $sampleJobsCollection->count()));
}

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('runBackgroundJob', fn() => runBackgroundJob())
    ->purpose('Run PHP classes and methods in the background')
    ->everyMinute()
    ->withoutOverlapping();
