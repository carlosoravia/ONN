<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-6">
            @if($lastLotto)
            <x-dashboard-header :lottosCount="$lottosCount" :lastLotto="$lastLotto"></x-dashboard-header>
            @else
            <div class="py-9 mb-6 text-white">
                <h1 class="text-5xl font-bold mb-2">Benvenuto <span class="text-azure-500">{{ Auth::user()->name }}</span></h1>
                <div class="mt-6 text-lg">
                    <p class="text-2xl">Non ci sono abbastanza dati per un resoconto</p>
                </div>
            </div>
            @endif
            <!-- Placeholder per moduli -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Modulo 1 -->
                <div class="bg-gray2-900 border border-gray-200 rounded-lg shadow p-4">
                    <h2 class="text-xl font-semibold mb-2 text-gray-100">Comandi</h2>
                    <p class="text-gray-100">Sezione comandi rapidi</p>
                    <div class="mt-4 w-full h-max flex justify-center text-center flex-col gap-3 uppercase font-bold">
                        <x-command-btn href="{{ route('lotto.today') }}">{{ __("Lotti creati in giornata") }}</x-command-btn>
                        <x-command-btn href="{{ route('admin.editUsers') }}">{{ __("Edita utenti") }}</x-command-btn>
                        <x-command-btn href="{{ route('front.show-all-lottos') }}">{{ __("Storico lotti") }}</x-command-btn>
                    </div>
                </div>
                <div class="bg-gray2-900 border border-gray-200 rounded-lg shadow p-4">
                    <h2 class="text-xl font-semibold mb-2 text-gray-100">Audit Logs</h2>
                    <p class="text-gray-100">Ultime azioni fatte dagli utenti</p>
                    <div class="rounded-lg border border-gray-400 overflow-hidden mt-4 p-0">
                        <table class="w-full h-max-content text-sm shadow">
                            <thead class="table w-full table-fixed">
                                <tr>
                                    <th class="px-4 py-2 text-gray-100 text-start w-2/4">Data</th>
                                    <th class="px-4 py-2 text-gray-100 text-start w-1/4">Azione</th>
                                    <th class="px-4 py-2 text-gray-100 text-start w-1/4">Utente</th>
                                    <th class="px-4 py-2 text-gray-100 text-start w-1/4"></th>
                                </tr>
                            </thead>
                            <tbody class="block max-h-[55vh] overflow-y-auto w-full">
                                @foreach ($audits as $a)
                                    <tr class="bg-gray-800 hover:bg-gray-700 border-b border-gray-600 table w-full table-fixed">
                                        <td class="px-4 py-2 text-gray-300 text-start w-2/4">{{ date_format($a->created_at, "H:i:s | d/m/Y") }}</td>
                                        <td class="px-4 py-2 text-gray-300 text-start w-1/4">{{ $a->action }}</td>
                                        <td class="px-4 py-2 text-gray-300 text-start w-1/4">{{ $a->user->name }}</td>
                                        <td class="px-4 py-2 text-gray-300 text-start w-1/4"><a href="{{ route('admin.auditLog', $a->id) }}" class="bg-azure-600 text-black font-bold rounded hover:bg-azure-400 transition p-3 w-full h-full text-center block">Dettagli</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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
