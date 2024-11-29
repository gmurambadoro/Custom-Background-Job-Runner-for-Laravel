<x-layout>
    <h3>{{ $job->command_text }}</h3>

    <table>
        <tbody>
        <tr>
            <th>FQCN</th>
            <td>{{ $job->fqcn }}</td>
        </tr>
        <tr>
            <th>Method</th>
            <td>{{ $job->method }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>{{ $job->status }}</td>
        </tr>

        @if ($job->output)
            <tr>
                <td colspan="2">
                    {{ $job->output }}
                </td>
            </tr>
        @endif
        </tbody>
    </table>
</x-layout>
