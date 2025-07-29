<x-app-layout class="relative">
    @if (count($lottos) > 0)
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
                    <tr class="table w-full text-white text-xl table-fixed">
                        <th class="border border-gray-900 px-2 py-1 uppercase w-1/4">Codice Lotto</th>
                        <th class="border border-gray-900 px-2 py-1 uppercase w-1/4">Descrizione</th>
                        <th class="border border-gray-900 px-2 py-1 uppercase w-1/4">Data Creazione</th>
                        <th class="border border-gray-900 px-2 py-1 uppercase w-1/4">Controlli</th>
                    </tr>
                </thead>
                <tbody class="block max-h-[45vh] overflow-y-auto w-full text-white">
                    @foreach ($lottos as $lotto)
                    <tr class="text-lg table w-full text-center table-fixed text-start">
                        <td class="border border-gray-900 w-1/4 p-5 ">{{$lotto->code_lotto}}</td>
                        <td class="border border-gray-900 w-1/4 p-5">{{$preassembleds[$loop->index][0]->description}}</td>
                        <td class="border border-gray-900 w-1/4 p-5">{{ date_format($lotto->created_at, 'd/m/Y | H:i') }}</td>
                        <td class="border border-gray-900 w-1/4 px-3"><a href="{{ route('lotto.edit', $lotto->id) }}" class="bg-green-500 text-black font-bold rounded hover:bg-green-400 transition p-3 my-2 text-center block">Edita</a><a href="{{ route('download.lotto', ['filename' => $lotto->code_lotto . '.pdf']) }}" class="bg-azure-600 text-black font-bold rounded hover:bg-azure-400 transition p-3 my-2 text-center block">Scarica</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </form>
        <div class="mt-4 w-full h-max flex justify-start">
            <a href="{{ Auth::user()->role == 'admin' ? route('admin.index') : route('operator.index') }}" class="bg-red-600 text-black font-semibold py-2 px-4 rounded hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-opacity-75">
                Torna alla home
            </a>
        </div>
    </div>
    @else
        <div class="max-w-6xl h-fit mx-auto my-0 border border-gray-900 p-4 rounded-lg shadow mt-9 text-white bg-gray-300 shadow-lg absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 p-9">
            <div class="text-center">
                <h2 class="text-4xl font-bold mb-4">Nessun lotto creato oggi</h2>
                <p class="text-xl">Non sono stati creati lotti per la giornata corrente.</p>
                <div class="flex justify-center mt-6 space-x-4">
                    <a href="{{ Auth::user()->role == 'admin' ? route('admin.index') : route('operator.index') }}" class="mt-4 w-1/2 bg-red-600 text-white font-semibold py-2 px-4 rounded hover:text-azure-600 hover:bg-gray-400 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                        Torna alla home
                    </a>
                    <a href="{{ route('select.preassembled') }}" class="mt-4 w-1/2 bg-cyan-600 text-white font-semibold py-2 px-4 rounded hover:text-azure-600 hover:bg-gray-400 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                        Crea lotto
                    </a>
                </div>
            </div>
        </div>
    @endif
    <x-footer />
</x-app-layout>
