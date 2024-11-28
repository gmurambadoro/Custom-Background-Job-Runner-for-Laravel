<?php

namespace App\Enum;

enum PhpExecStatusEnum : string
{
    case Pending = 'pending';
    case Running = 'running';
    case Completed = 'completed';
    case Failed = 'failed';
}
