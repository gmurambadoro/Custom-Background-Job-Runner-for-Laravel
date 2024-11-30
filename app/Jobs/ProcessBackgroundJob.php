<?php

namespace App\Jobs;

use App\Enums\JobStatusEnum;
use App\Models\BackgroundJob;
use App\Services\SimplePhpClassInvoker;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

/**
 * Job responsible for executing a background job.
 *
 * This job is designed to be queued, allowing it to run asynchronously in the background while still being able to track its progress and status.
 */
class ProcessBackgroundJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     *
     * @param BackgroundJob $backgroundJob The background job to be executed.
     */
    public function __construct(public BackgroundJob $backgroundJob)
    {
        //
    }

    /**
     * Execute the job.
     *
     * This method is called when the job is scheduled for execution. It calls the private `invokePhpClass` method to execute the underlying PHP class.
     *
     * @throws Throwable
     */
    public function handle(): void
    {
        $this->invokePhpClass(backgroundJob: $this->backgroundJob);
    }

    /**
     * Executes the underlying PHP class and stores the return result and output.
     *
     * This private method is responsible for executing the PHP class and handling any exceptions that may occur during execution.
     * It updates the job status to `Completed` if successful, or sets it to `Failed` with the exception message otherwise.
     *
     * @param BackgroundJob $backgroundJob The background job to be executed.
     */
    private function invokePhpClass(BackgroundJob $backgroundJob): void
    {
        try {
            // Invoke the PHP class using the SimplePhpClassInvoker service
            SimplePhpClassInvoker::invoke(
                fqcn: $backgroundJob->fqcn,
                method: $backgroundJob->method,
                static: $backgroundJob->is_static,
                arguments: $backgroundJob->arguments ?? [],
            );

            // Update the job status to Completed
            $backgroundJob->update(['status' => JobStatusEnum::Completed->value]);

            // todo: Log to stdout
        } catch (\Throwable $exception) {
            // Update the job status to Failed with the exception message
            $backgroundJob->update([
                'status' => JobStatusEnum::Failed->value,
                'output' => $exception->getMessage(),
            ]);

            // todo: Log to stderr
        }
    }
}
