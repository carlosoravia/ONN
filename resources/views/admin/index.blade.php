<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-6">
            <div class="py-9">
                <h1 class="text-3xl font-bold mb-2 text-white">Benvenuto <span class="text-azure-500">{{ Auth::user()->name }}</span></h1>
                <p class="text-white mb-6">Questa è la tua area di controllo. Aggiungi qui i comandi e gli strumenti principali.</p>
            </div>
            <!-- Placeholder per moduli -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Modulo 1 -->
                <div class="bg-gray2-900 border border-gray-200 rounded-lg shadow p-4">
                    <h2 class="text-xl font-semibold mb-2 text-gray-100">Modulo 1</h2>
                    <p class="text-gray-100">Qui potrai inserire un comando o una sezione informativa.</p>
                </div>

                <!-- Modulo 2 -->
                <div class="bg-gray2-900 border border-gray-200 rounded-lg shadow p-4">
                    <h2 class="text-xl font-semibold mb-2 text-gray-100">Audit Logs</h2>
                    <p class="text-gray-100">Ultime azioni fatte dagli utenti</p>
                    <table class="w-full h-max-content border border-gray-400 text-sm">
                        <thead class="text-left block w-full">
                            <tr>
                                <th class="px-4 py-2 text-gray-100">Data</th>
                                <th class="px-4 py-2 text-gray-100">Azione</th>
                                <th class="px-4 py-2 text-gray-100">Utente</th>
                            </tr>
                        </thead>
                        <tbody class="block max-h-[55vh] overflow-y-auto w-full">
                            @foreach ($audits as $a)
                                <tr class="bg-gray-800 hover:bg-gray-700">
                                    <td class="px-4 py-2 text-gray-300">{{ date_format($a->created_at, "H:i:s | d/m/Y") }}</td>
                                    <td class="px-4 py-2 text-gray-300">{{ $a->action }}</td>
                                    <td class="px-4 py-2 text-gray-300">{{ $a->user->name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Modulo 3 -->
                <div class="bg-gray2-900 border border-gray-200 rounded-lg shadow p-4 md:col-span-2">
                    <h2 class="text-xl font-semibold mb-2 text-gray-100">Modulo esteso</h2>
                    <p class="text-gray-100">Puoi usare questo spazio per grafici, tabelle o moduli interattivi.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
