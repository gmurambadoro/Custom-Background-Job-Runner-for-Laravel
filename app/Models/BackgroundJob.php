<?php

namespace App\Models;

use App\Enums\JobPriorityEnum;
use App\Enums\JobStatusEnum;
use App\Jobs\ProcessBackgroundJob;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents a background job in the application.
 */
final class BackgroundJob extends Model
{
    protected $table = 'background_jobs';

    protected $guarded = ['id'];

    protected $casts = [
        'arguments' => 'array', // Casts the arguments attribute as an array
        'status' => JobStatusEnum::class, // Casts the script attribute as an enum value
        'priority' => JobPriorityEnum::class, // Casts the priority attribute as an enum value
        'is_static' => 'boolean', // Casts the is_static attribute as a boolean
    ];

    protected $attributes = [
        'delay' => 0,
        'priority' => JobPriorityEnum::Low->value,
    ];

    /**
     * Returns the command text for the background job.
     *
     * @return Attribute
     */
    public function commandText(): Attribute
    {
        return new Attribute(
            get: function () {
                $args = collect($this->arguments)->map(fn($item) => sprintf('"%s"', $item))->join(", ");

                if ($this->is_static) {
                    return sprintf("Job #%s: %s::%s(%s)", $this->id, $this->fqcn, $this->method, $args);
                } else {
                    return sprintf('Job #%s: (new %s())->%s(%s)', $this->id, $this->fqcn, $this->method, $args);
                }
            },
        );
    }

    public function toLogMessage(): string
    {
        $props = collect([
            sprintf(
                "Job #: %s, FQCN: %s, Method: %s, Arguments: %s, Static Call: %s, Priority: %s, Status: %s",
                $this->id,
                $this->fqcn,
                $this->method,
                json_encode($this->arguments), // Encode array arguments as JSON for readability
                $this->is_static ? 'Yes' : 'No',
                $this->priority->name,
                $this->status->name,
            ),
        ]);

        if ($this->delay) {
            $props->add(", Delay: $this->delay sec");
        }

        if ($this->output) {
            $props->add(sprintf("%sMessage: %s", PHP_EOL, $this->output));
        }

        return $props->join("");
    }

    /**
     * Returns whether the job is failed.
     *
     * @return Attribute
     */
    public function failed(): Attribute
    {
        return new Attribute(
            get: fn() => $this->status->value === JobStatusEnum::Failed->value,
        );
    }

    /**
     * Returns whether the job is running.
     *
     * @return Attribute
     */
    public function running(): Attribute
    {
        return new Attribute(
            get: fn() => $this->status->value === JobStatusEnum::Running->value,
        );
    }

    /**
     * Dispatches the underlying background job.
     *
     * @return self
     */
    public function dispatch(): self
    {
        // Update the status to running
        $this->update(['status' => JobStatusEnum::Running->value]); // The job is now in the running state

        // Determine the queue based on the priority
        $queue = match ($this->priority) {
            JobPriorityEnum::High->value => 'high',
            JobPriorityEnum::Medium->value => 'medium',
            default => 'default',
        };

        if ($this->delay) {
            // Dispatch with delay
            ProcessBackgroundJob::dispatch($this)->delay($this->delay)->onQueue($queue);
        } else {
            // Dispatch without delay
            ProcessBackgroundJob::dispatch($this)->onQueue($queue);
        }

        \Log::channel('custom')->info(sprintf('Job #%s: Dispatched job [Status = %s]', $this->id, $this->status->name));
        \Log::channel('custom')->info($this->toLogMessage());

        return $this;
    }

    /**
     * Attempts to retry a failed job.
     *
     * @return void
     */
    public function attemptRetryIfFailed(): void
    {
        if ($this->failed) {
            $this->update([
                'status' => JobStatusEnum::Pending->value,
                'retry_count' => $this->retry_count + 1
            ]);

            $this->dispatch();
        }
    }
}
