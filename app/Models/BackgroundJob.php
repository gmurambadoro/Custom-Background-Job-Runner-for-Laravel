<?php

namespace App\Models;

use App\Enums\JobPriorityEnum;
use App\Enums\JobStatusEnum;
use App\Jobs\ProcessBackgroundJob;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

final class BackgroundJob extends Model
{
    protected $table = 'background_jobs';

    protected $guarded = ['id'];

    protected $casts = [
        'arguments' => 'array',
        'script' => JobStatusEnum::class,
        'priority' => JobPriorityEnum::class,
        'is_static' => 'boolean',
    ];

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

    public function failed(): Attribute
    {
        return new Attribute(
            get: fn() => $this->status === JobStatusEnum::Failed->value,
        );
    }

    public function running(): Attribute
    {
        return new Attribute(
            get: fn() => $this->status === JobStatusEnum::Running->value,
        );
    }

    /**
     * Dispatches the underlying background job
     * @return void
     */
    public function dispatch(): void
    {
        $this->update(['status' => JobStatusEnum::Running->value]); // The job is now in the running state

        $queue = match ($this->priority) { // determine the Queue to send this job to, based on its priority
            JobPriorityEnum::High->value => 'high',
            JobPriorityEnum::Medium->value => 'medium',
            default => 'default',
        };

        if ($this->delay) {
            ProcessBackgroundJob::dispatch($this)->delay($this->delay)->onQueue($queue);
        } else {
            ProcessBackgroundJob::dispatch($this)->onQueue($queue);
        }
    }
}
