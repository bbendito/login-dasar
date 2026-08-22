<x-app-layout>
    <div class="w-full max-w-md text-center space-y-6">
        <h1 class="text-2xl font-semibold">Dashboard</h1>

        <div class="bg-white dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg p-8 shadow-sm">
            <p class="text-sm text-[#706f6c] dark:text-[#A1A099] mb-1">Selamat datang,</p>
            <p class="text-xl font-semibold">{{ auth()->user()->name }}</p>
            <p class="text-sm text-[#706f6c] dark:text-[#A1A099] mt-1">{{ '@' }}{{ auth()->user()->username }}</p>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                type="submit"
                class="inline-flex justify-center rounded-md border border-[#e3e3e0] dark:border-[#3E3E3A] px-4 py-2 text-sm hover:border-[#19140035] transition"
            >
                Logout
            </button>
        </form>
    </div>
</x-app-layout>
