<x-app-layout>
    <div class="max-w-6xl h-fit mx-auto my-0 border border-gray-900 p-4 rounded shadow mt-9">
        <form wire:submit.prevent="submit">
            <div class="flex justify-between items-center border-b border-gray-300 pb-2 mb-4">
                <div>
                    <p class="text-sm text-white uppercase">Lista lotti della giornata</p>
                    {{-- <p class="text-sm">Prima emissione del 22/03/2024<br>Rev. 01 del 16/04/2024</p> --}}
                </div>
                <div class="text-right">
                    <img src="/images/logo_server_2.png" alt="Logo Onn Water" class="h-12 object-contain">
                </div>
            </div>
            <h2 class="text-xl font-bold text-center mb-4 border-y py-2 bg-gray-900 uppercase">Lotti</h2>
            <table class="w-full h-max-content border border-gray-900 text-sm">
                <thead class="bg-gray-200 text-left block w-full">
                    <tr class="table w-full table-fixed text-white text-xl">
                        <th class="border border-gray-900 px-2 py-1 uppercase w-1/4">Codice Lotto</th>
                        <th class="border border-gray-900 px-2 py-1 uppercase w-1/4">Descrizione</th>
                        <th class="border border-gray-900 px-2 py-1 uppercase w-1/4">Data Creazione</th>
                        <th class="border border-gray-900 px-2 py-1 uppercase w-1/12"></th>
                    </tr>
                </thead>
                <tbody class="block max-h-[45vh] overflow-y-auto w-full text-white">
                    @foreach ($lottos as $lotto)
                    <tr class="text-lg">
                        <td class="border border-gray-900 w-1/4 p-5 ">{{$lotto->code_lotto}}</td>
                        <td class="border border-gray-900 w-1/4 px-2 py-1">{{$preassembleds[$loop->index][0]->padre_description}}</td>
                        <td class="border border-gray-900 w-1/4 px-2 py-1">{{ date_format($lotto->created_at, 'd/m/Y | H:i') }}</td>
                        <td class="border border-gray-900 w-1/12 px-3"><a href="{{ route('lotto.edit', $lotto->id) }}" class="bg-azure-600 text-black font-bold rounded hover:bg-azure-400 transition p-3 w-full h-full text-center block">Edita</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </form>
        <div class="mt-4 w-full h-max flex justify-start">
            <a href="{{ Auth::user()->role ? route('admin.index') : route('operator.index') }}" class="bg-red-600 text-black font-semibold py-2 px-4 rounded hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-opacity-75">
                Torna alla home
            </a>
        </div>
    </div>
</x-app-layout>
