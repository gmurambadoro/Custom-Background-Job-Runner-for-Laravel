<?php

namespace App\Jobs;

use App\Enums\JobStatusEnum;
use App\Models\BackgroundJob;
use App\Services\SimplePhpClassInvoker;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class ProcessBackgroundJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public BackgroundJob $backgroundJob)
    {
        //
    }

    /**
     * Execute the job.
     * @throws Throwable
     */
    public function handle(): void
    {
        $this->invokePhpClass(backgroundJob: $this->backgroundJob);
    }

    /**
     * Executes the underlying PHP class and stores the return result and output
     * @param BackgroundJob $backgroundJob
     * @return void
     */
    private function invokePhpClass(BackgroundJob $backgroundJob): void
    {
        try {
            SimplePhpClassInvoker::invoke(
                fqcn: $backgroundJob->fqcn,
                method: $backgroundJob->method,
                static: $backgroundJob->is_static,
                arguments: $backgroundJob->arguments ?? [],
            );

            $backgroundJob->update(['status' => JobStatusEnum::Completed->value]);

            // todo: Log to stdout
        } catch (\Throwable $exception) {
            $backgroundJob->update([
                'status' => JobStatusEnum::Failed->value,
                'output' => $exception->getMessage(),
            ]);

            // todo: Log to stderr
        }
    }
}
