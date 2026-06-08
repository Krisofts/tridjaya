<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'App')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 text-gray-800">

    <!-- Navbar -->
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between">
            <div class="font-bold text-blue-600">CRM App</div>

            <div class="space-x-4 text-sm">
                <a href="#" class="hover:text-blue-600">Leads</a>
                <a href="#" class="hover:text-blue-600">Dashboard</a>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main class="max-w-7xl mx-auto p-6">
        @yield('content')
    </main>

</body>
</html>