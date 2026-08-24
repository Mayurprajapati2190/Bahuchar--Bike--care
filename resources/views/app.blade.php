<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#020617">
        <link rel="icon" href="/logo-icon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/pwa-icon.svg">
        <meta name="description" content="Bahuchar Bike Care — two-wheeler service in Gota, Ahmedabad">
        <link rel="manifest" href="/build/manifest.webmanifest">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <title inertia>{{ config('app.name', 'Bahuchar Bike Care') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
