<?php

namespace App\Console\Commands;

use App\Services\SimplePhpClassInvoker;
use Illuminate\Console\Command;

final class BackgroundExec extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:background-exec
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
        $arguments = $this->argument('arguments');
        $static = (bool)$this->option('static');

        SimplePhpClassInvoker::runBackgroundJob(
            fqcn: $fqcn,
            method: $method,
            static: $static,
            arguments: $arguments,
        );
    }
}
