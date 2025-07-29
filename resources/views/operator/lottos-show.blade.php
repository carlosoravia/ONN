<x-app-layout>
    <div class="max-w-6xl h-[80vh] mx-auto border border-gray-300 dark:border-gray-600 p-4 rounded shadow overflow-y-auto m-5">
        <form wire:submit.prevent="submit">
            <div class="flex justify-between items-center border-b border-gray-300 dark:border-gray-600 pb-2 mb-4">
                <div>
                    <p class="text-sm">LISTA LOTTI</p>
                    {{-- <p class="text-sm">Prima emissione del 22/03/2024<br>Rev. 01 del 16/04/2024</p> --}}
                </div>
                <div class="text-right">
                    <img src="/images/logo_server_2.png" alt="Logo Onn Water" class="h-12 object-contain">
                </div>
            </div>
            <h2 class="text-xl font-bold text-center mb-4 border-y py-2 bg-blue-50 dark:bg-blue-900 dark:text-blue-200">LOTTI</h2>
            <table class="w-full h-max-content border border-gray-400 dark:border-gray-600 text-sm">
                <thead class="bg-gray-200 dark:bg-gray-700 text-left">
                    <tr>
                        <th class="border border-gray-400 dark:border-gray-600 px-2 py-1 uppercase">Contenuto<br><span class="text-xs font-normal">(descrizione del lotto)</span></th>
                        <th class="border border-gray-400 dark:border-gray-600 px-2 py-1 uppercase">Seriale<br><span class="text-xs">ONN WATER</span></th>
                        <th class="border border-gray-400 dark:border-gray-600 px-2 py-1 uppercase"></th>
                    </tr>
                </thead>
                <tbody style="overflow-y: auto; height: ;">
                    @forelse ($lottos as $l)
                    <tr>
                        <td class="border border-gray-300 dark:border-gray-600 p-5 ">{{$preassembleds[$loop->index]->padre_description}}</td>
                        <td class="border border-gray-300 dark:border-gray-600 px-2 py-1">{{$l->code_lotto}}</td>
                        <td class="border border-gray-300 dark:border-gray-600">
                            <a href="{{route('lotto.edit', $l->id)}}" class="bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                                seleziona
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center py-4 text-gray-500">
                            Nessun lotto presente.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </form>
    </div>
    <x-footer />
</x-app-layout>
