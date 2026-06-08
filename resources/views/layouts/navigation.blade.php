<!-- resources/views/layouts/navigation.blade.php -->

<div class="flex min-h-screen bg-gray-100">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-white border-r border-gray-200">

        <div class="p-6 font-bold text-lg border-b">
            {{ config('app.name', 'Laravel') }}
        </div>

        <nav class="p-4 space-y-2">

            <a href="{{ route('dashboard') }}"
               class="block px-4 py-2 rounded hover:bg-gray-100">
                Dashboard
            </a>

            <a href="{{ route('crm.leads.index') }}"
               class="block px-4 py-2 rounded hover:bg-gray-100">
                Leads
            </a>

            <a href="{{ route('users.index') }}"
               class="block px-4 py-2 rounded hover:bg-gray-100">
                Users
            </a>

        </nav>

        <!-- LOGOUT -->
        <div class="absolute bottom-0 w-64 p-4 border-t">
            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit"
                        class="w-full text-left px-4 py-2 rounded hover:bg-red-50 text-red-600">
                    Logout
                </button>
            </form>
        </div>

    </aside>

    <!-- MAIN CONTENT -->
    <div class="flex-1">
        <!-- Page Content -->
        {{ $slot ?? '' }}
    </div>

</div>