<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <title>{{ $title ?? config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/pico.min.css') }}">
</head>
<body>
<main class="container">
    <nav>
        <ul>
            <li>
                <hgroup>
                    <h2>
                        <a href="{{ route('background-jobs.index') }}">{{ config('app.name') }}</a>
                    </h2>
                    <p>A custom system to execute PHP classes as background jobs, independent of Laravel's built-in
                        queue system</p>
                </hgroup>

                <hr/>
            </li>
        </ul>
        <ul>
            @unless(\Illuminate\Support\Facades\Route::is('background-jobs.create'))
                <li>
                    <a href="{{ route('background-jobs.create') }}" role="button" class="contrast">+ New Job</a>
                </li>
            @endunless
        </ul>
    </nav>

    {{ $slot }}
</main>
<footer></footer>
</body>
</html>
