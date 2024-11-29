<?php

namespace App\Models;

use App\Enums\PhpExecStatusEnum;
use App\Enums\PhpJobPriorityEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class PhpExecCommandModel extends Model
{
    protected $table = 'php_exec_commands';

    protected $guarded = ['id'];

    protected $casts = [
        'arguments' => 'array',
        'script' => PhpExecStatusEnum::class,
        'priority' => PhpJobPriorityEnum::class,
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
            get: fn() => $this->status === PhpExecStatusEnum::Failed->value,
        );
    }
}
