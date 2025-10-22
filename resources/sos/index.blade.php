<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rides - Swas Cabs</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100">
    @include('layouts.partials.sidebar')
    <div class="ml-72 p-6">
        <h1 class="text-2xl font-bold text-primary mb-6">Rides</h1>
        <div class="bg-white p-6 rounded-lg shadow">
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="text-left">Passenger</th>
                        <th class="text-left">Driver</th>
                        <th class="text-left">Status</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</body>
</html>