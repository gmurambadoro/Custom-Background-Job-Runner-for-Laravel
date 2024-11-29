<?php

namespace App\Enums;

enum PhpJobStatusEnum: string
{
    case Pending = 'pending';
    case Running = 'running';
    case Completed = 'completed';
    case Failed = 'failed';
}
