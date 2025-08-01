<div class="max-w-6xl h-fit mx-auto my-0 border border-gray-900 p-4 rounded shadow my-5">
    <div class="flex justify-between items-center pb-2 mb-4">
        <div>
            <p class="text-sm text-white">LISTA PRE-ASSEMBLATI</p>
        </div>
        <div class="text-right">
            <img src="/images/logo_server_2.png" alt="Logo Onn Water" class="h-12 object-contain">
        </div>
    </div>
    <h2 class="text-xl font-bold text-center mb-4 border-y py-2 bg-blue-50">PRE-ASSEMBLATI</h2>
    <input wire:model.live="query" placeholder="Cerca Per Nome..." class="border border-gray-400 focus:text-dark text-dark bg-white px-3 py-1 mt-1 rounded mb-4">
    <input wire:model.live="queryCode" placeholder="Cerca Per Codice..." class="border border-gray-400 focus:text-dark text-dark bg-white px-3 py-1 mt-1 rounded mb-4">
    <div class="h-[32vh] w-full mt-20" wire:loading>
        <x-loader size="12" color="azure-500" message="Caricamento preassemblati..." />
    </div>
    <table class="w-full h-max-content border border-gray-900 text-sm" wire:loading.remove>
        <thead class="bg-gray-200 text-left block w-full">
            <tr class="table w-full table-fixed text-white">
                <th class="border border-gray-900 px-2 py-1 uppercase w-2/4">descrizione<br><span class="text-xs font-normal">(componenti necessari per l'assemblaggio)</span></th>
                <th class="border border-gray-900 px-2 py-1 uppercase w-1/4">codice pre-assemblato<br><span class="text-xs">ONN WATER</span></th>
                <th class="border border-gray-900 px-2 py-1 uppercase w-1/4"></th>
            </tr>
        </thead>
        <tbody class="block max-h-[40vh] overflow-y-auto w-full">
            @foreach ($preassembleds as $p)
            <tr class="table w-full table-fixed text-white">
                <td class="border border-gray-300 w-2/4 p-5 ">{{$p->padre_description}}</td>
                <td class="border border-gray-300 w-1/4 px-2 py-1">{{$p->code}}</td>
                <td class="border border-gray-300 w-1/4 p-0">
                    <div class="flex justify-center items-center h-full px-3">
                        <a href="{{route('lotto.create', $p->id)}}" class="bg-blue-600 text-white rounded hover:text-azure-600 hover:bg-gray-400 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out p-3 w-full h-full text-center">
                            Seleziona
                        </a>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-4 w-full h-max flex justify-start">
        <a href="{{ Auth::user()->role == 'Admin' ? route('admin.index') : route('operator.index') }}" class="bg-red-800 text-white font-semibold py-2 px-4 rounded hover:text-azure-600 hover:bg-gray-400 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
            Torna alla home
        </a>
    </div>
</div>
