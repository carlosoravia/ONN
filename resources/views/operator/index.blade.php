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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray2-900 border border-gray-200 rounded-lg shadow p-4">
                    <h2 class="text-xl font-semibold mb-2 text-gray-100">Comandi</h2>
                    <p class="text-gray-100">Sezione comandi rapidi</p>
                    <div class="mt-4 w-full h-max flex justify-center text-center flex-col gap-3 uppercase font-bold">
                        <x-command-btn href="{{ route('lotto.today') }}">{{ __("Lotti creati in giornata") }}</x-command-btn>
                        <x-command-btn href="{{ route('select.preassembled') }}">{{ __("Crea nuovo lotto") }}</x-command-btn>
                        <x-command-btn href="{{ route('front.show-all-lottos') }}">{{ __("Vedi tutti i lotti") }}</x-command-btn>
                    </div>
                </div>
                <div class="bg-gray2-900 border border-gray-200 rounded-lg shadow p-4">
                    <h2 class="text-xl font-semibold mb-2 text-gray-100">Ultimi lotti creati</h2>
                    <p class="text-gray-100">Vedi gli ultimi lotti creati e editali se serve</p>
                    <div class="h-[35vh] overflow-y-auto block mt-4 px-2 border border-gray-400 rounded-lg">
                        <table class="w-full h-max-content text-sm shadow">
                            <thead class="table w-full table-fixed">
                                <tr>
                                    <th class="px-4 py-2 text-gray-100 text-start w-1/3">Codice Lotto</th>
                                    <th class="px-4 py-2 text-gray-100 text-start w-1/3">Descrizione</th>
                                    <th class="px-4 py-2 text-gray-100 text-start w-1/3"></th>
                                </tr>
                            </thead>
                            <tbody class="block max-h-[30vh] overflow-y-auto w-full">
                                @foreach ($lottos as $lotto)
                                    <tr class="bg-gray-900 border-b border-gray-600 table w-full table-fixed mb-2 shadow">
                                        <td class="px-4 py-2 text-gray-300 text-start">{{ $lotto->code_lotto }}</td>
                                        <td class="px-4 py-2 text-gray-300 text-start">{{ $preassembleds[$loop->index]->padre_description }}</td>
                                        <td class="px-4 py-2 text-gray-300 text-start">
                                            <a href="{{ route('lotto.edit', $lotto->id) }}" class="bg-azure-300 text-white font-bold rounded hover:bg-azure-400 transition p-3 w-full h-full text-center block">Edita</a>
                                        </td>
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
