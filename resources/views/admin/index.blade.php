<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-6">
            <div class="py-9">
                <h1 class="text-3xl font-bold mb-2 text-gray-900 dark:text-white">Benvenuto <span class="text-blue-300">{{ Auth::user()->name }}</span></h1>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Questa è la tua area di controllo. Aggiungi qui i comandi e gli strumenti principali.</p>
            </div>
            <!-- Placeholder per moduli -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Modulo 1 -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow p-4">
                    <h2 class="text-xl font-semibold mb-2 text-gray-800 dark:text-white">Modulo 1</h2>
                    <p class="text-gray-600 dark:text-gray-400">Qui potrai inserire un comando o una sezione informativa.</p>
                </div>

                <!-- Modulo 2 -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow p-4">
                    <h2 class="text-xl font-semibold mb-2 text-gray-800 dark:text-white">Modulo 2</h2>
                    <p class="text-gray-600 dark:text-gray-400">Ad esempio log, statistiche o controlli rapidi.</p>
                </div>

                <!-- Modulo 3 -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow p-4 md:col-span-2">
                    <h2 class="text-xl font-semibold mb-2 text-gray-800 dark:text-white">Modulo esteso</h2>
                    <p class="text-gray-600 dark:text-gray-400">Puoi usare questo spazio per grafici, tabelle o moduli interattivi.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
