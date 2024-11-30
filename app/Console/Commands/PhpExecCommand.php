<?php

namespace App\Console\Commands;

use App\Enums\JobStatusEnum;
use App\Http\Requests\BackgroundJobRequest;
use App\Models\BackgroundJob;
use Illuminate\Console\Command;
use Log;
use Throwable;
use Validator;

/**
 * Console command responsible for executing a PHP class method in the background.
 *
 * This command provides a flexible way to invoke methods on PHP classes, allowing for
 * customization of object instantiation and method invocation. The command also handles
 * validation of input parameters and logging of job execution.
 */
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
                            {--priority=0 : Priority of the job, either 0, 1 or 2}
                            {--delay=0 : Delay in seconds - the job will be executed only after the specified delay}
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
        $arguments = collect($this->argument('arguments') ?? []);
        $static = (bool)$this->option('static') ?? false;
        $priority = (int)$this->option('priority');
        $delay = (int)$this->option('delay');

        try {
            $isStaticCall = $static ? '--static' : '';
            $isDelayedCall = $delay ? "--delay=$delay" : '';

            $message = <<<HEREDOC
$this->name $fqcn $method {$arguments->join(", ")} $isStaticCall --priority=$priority $isDelayedCall
HEREDOC;;

            Log::channel('custom')->info(str($message)->trim()->toString());

            // Validate input parameters using a custom request validation rule
            $validated = Validator::make(
                data: [
                    'fqcn' => $fqcn,
                    'method' => $method,
                    'arguments' => $arguments->toArray(),
                    'is_static' => $static,
                    'status' => JobStatusEnum::Pending->value,
                    'priority' => $priority,
                    'delay' => $delay,
                ],
                rules: BackgroundJobRequest::getValidationRules(),
            )->validate();

            // Create a new background job instance and dispatch it
            BackgroundJob::create($validated)->dispatch();
        } catch (Throwable $exception) {
            // Handle any exceptions that occur during command execution
            Log::channel('custom')->error(sprintf('Error: %s', $exception->getMessage()));
        }
    }
}
