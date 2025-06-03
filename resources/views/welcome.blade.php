<x-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900 px-4">
        <div class="max-w-md w-full bg-white dark:bg-gray-800 p-6 rounded shadow-md">

            <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white text-center">Accedi al tuo account</h2>

            @if (session('status'))
                <div class="mb-4 text-green-600 dark:text-green-400">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm text-gray-700 dark:text-gray-300">Email</label>
                    <input id="email" type="email" name="email" required autofocus
                        class="mt-1 block w-full px-4 py-2 rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm text-gray-700 dark:text-gray-300">Password</label>
                    <input id="password" type="password" name="password" required
                        class="mt-1 block w-full px-4 py-2 rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <!-- Ricordami -->
                <div class="flex items-center">
                    <input id="remember" type="checkbox" name="remember" class="h-4 w-4 text-blue-600 border-gray-300 rounded dark:bg-gray-800 dark:border-gray-600">
                    <label for="remember" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                        Ricordami
                    </label>
                </div>

                <!-- Submit -->
                <div>
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition">
                        Accedi
                    </button>
                </div>

                <!-- Link password dimenticata -->
                <div class="text-center">
                    <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:underline dark:text-blue-400">
                        Password dimenticata?
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layout>
