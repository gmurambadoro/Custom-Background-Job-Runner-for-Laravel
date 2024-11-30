<?php

namespace App\Jobs;

use App\Enums\JobStatusEnum;
use App\Models\BackgroundJob;
use App\Services\SimplePhpClassInvoker;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Log;
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
        Log::channel('custom')->info(sprintf('Job #%s: Invoking command [ %s ]..', $this->backgroundJob->id, $this->backgroundJob->command_text));
        $this->invokePhpClass(backgroundJob: $this->backgroundJob);
        Log::channel('custom')->info(sprintf('Job #%s: Finished invoking command [ %s ] [ Status: %s ]', $this->backgroundJob->id, $this->backgroundJob->refresh()->command_text, $this->backgroundJob->refresh()->status->name));
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
            Log::channel('custom')->info($backgroundJob->command_text);

            SimplePhpClassInvoker::invoke(
                fqcn: $backgroundJob->fqcn,
                method: $backgroundJob->method,
                static: $backgroundJob->is_static,
                arguments: $backgroundJob->arguments ?? [],
            );

            // Update the job status to Completed
            $backgroundJob->update(['status' => JobStatusEnum::Completed->value]);
        } catch (Throwable $exception) {
            // Update the job status to Failed with the exception message
            $backgroundJob->update([
                'status' => JobStatusEnum::Failed->value,
                'output' => $exception->getMessage(),
            ]);

            Log::channel('custom')->error(sprintf("Job #%s: Error encountered :: %s", $backgroundJob->id, $exception->getMessage()));
        }
    }

    /**
     * Determine number of times the job may be attempted.
     */
    public function tries(): int
    {
        return (int)config('queues.retries', 3);
    }
}
