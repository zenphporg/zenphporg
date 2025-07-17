<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'Starter Kit') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:100,200,300,400,500,600,700,800,900" rel="stylesheet" />

    @vite(['resources/js/app.ts'])
    @inertiaHead
  </head>

  <body class="font-sans antialiased">
    @inertia
  </body>

</html>
