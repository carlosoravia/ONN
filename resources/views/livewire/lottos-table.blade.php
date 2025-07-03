<div class="max-w-6xl h-fit mx-auto my-0 border border-gray-300 p-4 rounded shadow m-5">
    <div class="flex justify-between items-center border-b border-gray-300 pb-2 mb-4">
        <div>
            <p class="text-sm text-white uppercase">LISTA LOTTI</p>
        </div>
        <div class="text-right">
            <img src="/images/logo_server_2.png" alt="Logo Onn Water" class="h-12 object-contain">
        </div>
    </div>
    <h2 class="text-xl font-bold text-center mb-4 border-y py-2 bg-gray-900 uppercase">Lotti</h2>
    <input wire:model.live="query" placeholder="Cerca Per Nome..." class="border border-gray-400 focus:text-dark text-dark bg-white px-3 py-1 mt-1 rounded mb-4">
    <input wire:model.live="queryCode" placeholder="Cerca Per Codice..." class="border border-gray-400 focus:text-dark text-dark bg-white px-3 py-1 mt-1 rounded mb-4">
    <input wire:model.live="queryDate" placeholder="Cerca Per Data..." class="border border-gray-400 focus:text-dark text-dark bg-white px-3 py-1 mt-1 rounded mb-4">
    <table class="w-full h-max-content border border-gray-900 text-sm">
        <thead class="bg-gray-200 text-left block w-full">
            <tr class="table w-full table-fixed text-white">
                <th class="border border-gray-900 px-2 py-1 uppercase w-2/4">descrizione<br><span class="text-xs font-normal">(componenti necessari per l'assemblaggio)</span></th>
                <th class="border border-gray-900 px-2 py-1 uppercase w-1/4">codice pre-assemblato<br><span class="text-xs">ONN WATER</span></th>
                <th class="border border-gray-900 px-2 py-1 uppercase w-1/4">Data di creazione<br><span class="text-xs">(G | M | A )</span></th>
                <th class="border border-gray-900 px-2 py-1 uppercase w-1/4"></th>
            </tr>
        </thead>
        <tbody class="block max-h-[55vh] overflow-y-auto w-full">
            @forelse ($lottos as $l)
            <tr class="table w-full table-fixed text-white">
                <td class="border border-gray-300 w-2/4 p-5 ">{{$l->preassembled->description}}</td>
                <td class="border border-gray-300 w-1/4 px-2 py-1">{{$l->code_lotto}}</td>
                <td class="border border-gray-300 w-1/4 px-2 py-1">{{date_format($l->created_at,'d M Y')}}</td>
                <td class="border border-gray-300 w-1/4 p-0">
                    <div class="flex justify-center items-center h-full px-3">
                        <a href="{{route('lotto.edit', $l->id)}}" class="bg-blue-600 text-white rounded hover:bg-blue-700 transition p-3 w-full h-full text-center">
                            Seleziona
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center py-4 text-white w-full p-5 text-3xl">
                    @if ($query)
                        Nessun lotto presente per la ricerca: {{ $query }}
                    @elseif($queryDate)
                        Nessun lotto prensente il giorno: {{ $queryDate }}
                    @elseif($queryCode)
                        Nessun lotto trovato con il codice: {{ $queryCode }}
                    @endif
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-4 w-full h-max flex justify-start">
        <a href="{{ Auth::user()->role == 'admin' ? route('admin.index') : route('operator.index') }}" class="bg-red-800 text-white font-semibold py-2 px-4 rounded hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-opacity-75">
            Torna alla home
        </a>
    </div>
</div>
