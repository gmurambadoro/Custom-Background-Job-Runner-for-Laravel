<?php

namespace App\Enums;

/**
 * Enum for job status levels.
 *
 * This enum provides a set of predefined values for job statuses, allowing for easy switching between different states.
 */
enum JobStatusEnum: string
{
    case Pending = 'pending';
    case Running = 'running';
    case Completed = 'completed';
    case Failed = 'failed';
}
