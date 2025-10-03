<nav class="bg-white shadow-md p-4 flex justify-between items-center">
    <div class="flex items-center">
        <button id="sidebar-toggle" class="text-secondary hover:text-primary focus:outline-none md:hidden">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
        <h1 class="text-xl font-semibold text-primary ml-4">@yield('title', 'Admin Panel')</h1>
    </div>
    <div class="flex items-center space-x-4">
       
        <form  method="POST">
            @csrf
            <button type="submit" class="text-secondary hover:text-primary">Logout</button>
        </form>
    </div>
</nav>