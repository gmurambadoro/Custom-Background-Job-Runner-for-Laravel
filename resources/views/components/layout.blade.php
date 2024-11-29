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
                    <h2>{{ config('app.name') }}</h2>
                    <p>A custom system to execute PHP classes as background jobs, independent of Laravel's built-in
                        queue system</p>
                </hgroup>

                <hr/>
            </li>
        </ul>
        <ul>
            <li><a href="#">+ New Job</a></li>
        </ul>
    </nav>

    {{ $slot }}
</main>
<footer></footer>
</body>
</html>
