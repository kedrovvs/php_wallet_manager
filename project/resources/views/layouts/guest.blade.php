<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Vault') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-900">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <a href="/" class="flex items-center gap-2.5 mb-6">
                <x-application-logo class="w-10 h-10 text-emerald-600" />
                <span class="text-2xl font-bold text-slate-900">Vault</span>
            </a>

            <div class="w-full sm:max-w-md px-6 py-6 bg-white shadow-sm border border-slate-200 sm:rounded-xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
