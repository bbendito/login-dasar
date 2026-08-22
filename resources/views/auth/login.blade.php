<x-app-layout>
    <div class="w-full max-w-sm">
        <h1 class="text-2xl font-semibold mb-6 text-center">Login</h1>

        <form method="POST" action="{{ route('login.store') }}" class="bg-white dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg p-6 shadow-sm space-y-4">
            @csrf

            <div>
                <label for="username" class="block text-sm font-medium mb-1.5">Username</label>
                <input
                    id="username"
                    name="username"
                    type="text"
                    value="{{ old('username') }}"
                    required
                    autofocus
                    autocomplete="username"
                    class="w-full rounded-md border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-[#F53003] focus:ring-[#F53003]"
                >
                @error('username')
                    <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium mb-1.5">Password</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    class="w-full rounded-md border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-[#F53003] focus:ring-[#F53003]"
                >
                @error('password')
                    <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="remember" class="rounded border-[#e3e3e0] dark:border-[#3E3E3A]">
                Ingat saya
            </label>

            <button
                type="submit"
                class="w-full inline-flex justify-center rounded-md bg-[#1b1b18] dark:bg-[#EEE] px-4 py-2.5 text-sm font-medium text-white dark:text-[#1b1b18] hover:opacity-90 transition"
            >
                Masuk
            </button>
        </form>
    </div>
</x-app-layout>
