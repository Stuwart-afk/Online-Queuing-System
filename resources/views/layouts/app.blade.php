<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        {{ $title ?? config('app.name') }}
    </title>


    <!-- Application CSS / JS -->

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])


    <!-- Queue Tracking CSS -->

    <link
        rel="stylesheet"
        href="{{ asset('css/number-tracking.css') }}"
    >


    @livewireStyles

</head>


<body>

    {{ $slot }}


    @livewireScripts

</body>

</html>
