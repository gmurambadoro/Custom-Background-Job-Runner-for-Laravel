<?php

namespace App\Models;

use App\Enums\PhpExecStatusEnum;
use Illuminate\Database\Eloquent\Model;

class PhpExecCommandModel extends Model
{
    protected $table = 'php_exec_commands';

    protected $guarded = ['id'];

    protected $casts = [
        'arguments' => 'array',
        'script' => PhpExecStatusEnum::class,
        'is_static' => 'boolean',
    ];
}
