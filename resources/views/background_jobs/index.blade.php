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
                    <th>Priority</th>
                    <th>Status</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($jobs as $job)
                    <tr>
                        <td>{{ $loop->index + $jobs->firstItem() }}.</td>
                        <td>{{ $job->created_at->format('d M Y, H:i:s A') }}</td>
                        <td>
                            <samp>{{ $job->command_text }}</samp>
                        </td>
                        <td>
                            <samp>{{ $job->priority->name }}</samp>
                        </td>
                        <td>{{ $job->status }}</td>
                        <td style="display: flex; gap: 2px;" nowrap="">
                            <a href="{{ route('background-jobs.show', $job) }}" role="button"
                               class="secondary">Details</a>

                            @if($job->failed)
                                <a href="{{ route('background-jobs.retry', $job) }}" role="button">
                                    Retry @if($job->retry_count)
                                        ({{ $job->retry_count }})
                                    @endif
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div>
            {{ $jobs->links() }}
        </div>
    @endif
</x-layout>
