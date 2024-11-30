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
        'script' => JobStatusEnum::class, // Casts the script attribute as an enum value
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
                    return sprintf("%s::%s(%s)", $this->fqcn, $this->method, $args);
                } else {
                    return sprintf('(new %s())->%s(%s)', $this->fqcn, $this->method, $args);
                }
            },
        );
    }

    /**
     * Returns whether the job is failed.
     *
     * @return Attribute
     */
    public function failed(): Attribute
    {
        return new Attribute(
            get: fn() => $this->status === JobStatusEnum::Failed->value,
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
            get: fn() => $this->status === JobStatusEnum::Running->value,
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
