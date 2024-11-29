<?php

namespace App\Console\Commands;

use App\Enums\PhpExecStatusEnum;
use App\Models\PhpExecCommandModel;
use Illuminate\Console\Command;

final class PhpExecCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:php-exec
                            {fqcn : Fully Qualified Class Name (FQCN) e.g. App\Model\User}
                            {method : The method to invoke on the FQCN instance e.g. create}
                            {arguments?* : Arguments in the order expected by the method signature}
                            {--static : Whether the invocation is static or not. A static invocation is invoked on the class directly and not on it\'s object instance.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Executes a class in the background, outside of Laravel\'s main application process';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $fqcn = $this->argument('fqcn');
        $method = $this->argument('method');
        $arguments = $this->argument('arguments') ?? [];
        $static = (bool)$this->option('static') ?? false;

        try {
            $message = sprintf('%s: Received payload for command {%s}::{%s} static=%s.', $this->name, $fqcn, $method, $static ? 'true' : 'false');

            $this->info($message);

            \Validator::make(compact('fqcn', 'method', 'arguments', 'static'), rules: [
                'fqcn' => 'required',
                'method' => 'required',
                'arguments' => 'nullable|array',
                'static' => 'boolean',
            ])->validate();

            $command = PhpExecCommandModel::create([
                'fqcn' => $fqcn,
                'method' => $method,
                'arguments' => $arguments,
                'is_static' => $static,
                'status' => PhpExecStatusEnum::Pending->value,
            ]);

            $this->info($message = sprintf('Saved command %s', $command->command_text));

            \Log::info($message, compact(['fqcn', 'method', 'arguments', 'static']));
        } catch (\Throwable $exception) {
            $errorMessage = sprintf('Error: {%s}::{%s} %s', $fqcn, $method, $exception->getMessage());
            $this->error($errorMessage);
            \Log::error($errorMessage);
        }
    }
}
