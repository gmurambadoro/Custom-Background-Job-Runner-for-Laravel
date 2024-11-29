<x-layout>
    @if(empty($jobs->count()))
        <div>
            <p>No jobs have been run yet. Run the following command in your terminal to get started:</p>

            <pre>$ php artisan help app:php-exec</pre>
        </div>
    @else
        <p>The following is a history of commands that have been run. Run the following command in your terminal to add
            more commands.</p>

        <pre>$ php artisan help app:php-exec</pre>

        <div class="overflow-auto">
            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Timestamp</th>
                    <th>Job</th>
                    <th>Status</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($jobs as $job)
                    <tr>
                        <td>{{ $loop->iteration }}.</td>
                        <td>{{ $job->created_at->format('d M Y, H:i:s A') }}</td>
                        <td>
                            <samp>{{ $job->command_text }}</samp>

                            @if($job->output)
                                <br/>

                                <pre>{{ $job->output }}</pre>
                            @endif
                        </td>
                        <td>{{ $job->status }}</td>
                        <td>
                            @if($job->failed)
                                <a role="button" href="#">Retry</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-layout>
