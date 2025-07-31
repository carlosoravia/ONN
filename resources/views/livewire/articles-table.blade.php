<div class="max-w-6xl h-fit mx-auto my-0 border border-gray-900 p-4 rounded shadow my-5">
    @if($table)
    <div class="flex justify-between items-center border-b border-gray-300 pb-2 mb-4">
        <div>
            <p class="text-sm text-white uppercase">LISTA articoli</p>
        </div>
        <div class="text-right">
            <img src="/images/logo_server_2.png" alt="Logo Onn Water" class="h-12 object-contain">
        </div>
    </div>
    <h2 class="text-xl font-bold text-center mb-4 border-y py-2 bg-blue-50 uppercase">Articoli</h2>
    <div class="">
        <input wire:model.live="query" placeholder="Cerca Per Codice..." class="h-[80%] border border-gray-400 focus:text-dark text-dark bg-white px-3 py-1 mt-1 rounded mb-4">
        <input wire:model.live="queryDescription" placeholder="Cerca in descrizione..." class="h-[80%] border border-gray-400 focus:text-dark text-dark bg-white px-3 py-1 mt-1 rounded mb-4">
        <div class="mb-4 items-center w-full">
            <label for="is_moca_check" class="text-white">Solo moca</label>
            <input id="is_moca_check" wire:model.live="queryMoca" type="checkbox" class="border border-gray-400 w-5 h-5 rounded">
        </div>
    </div>
    <div class="h-[32vh] w-full mt-20" wire:loading>
        <x-loader size="12" color="azure-500" message="Caricamento articoli..." />
    </div>
    <table class="w-full h-max-content border border-gray-900 text-sm overflow-hidden" wire:loading.remove>
        <thead class="bg-gray-200 text-left text-white">
            <tr class="table w-full table-fixed">
                <th class="border border-gray-900 px-2 py-1 uppercase w-1/4">Codice</th>
                <th class="border border-gray-900 px-2 py-1 uppercase w-1/4">Descrizione</th>
                <th class="border border-gray-900 px-2 py-1 uppercase w-1/4">Is_moca</th>
                <th class="border border-gray-900 px-2 py-1 uppercase w-1/4">Controlli</th>
            </tr>
        </thead>
        <tbody class="block max-h-[39vh] overflow-y-auto w-full text-white">
            @forelse ($articles as $a)
            <tr class="table w-full table-fixed">
                <td class="border border-gray-900 w-1/4 p-5 ">{{$a->code}}</td>
                <td class="border border-gray-900 w-1/4 px-2 py-1">{{$a->description}}</td>
                <td class="border border-gray-900 w-1/4 px-2 py-1 {{ $a->is_moca == '1' ? 'text-green-400' : 'text-white' }}">{{$a->is_moca}}</td>
                <td class="grid grid-cols-2 place-items-center px-3 py-1 w-fit">
                    @if ($a->is_moca == '1')
                        <form action="{{ route('article.update', $a->id) }}" method="POST" class="mx-3 inline">
                        @csrf
                            <button type="submit" class="bg-blue-600 text-white rounded hover:text-azure-600 hover:bg-gray-400 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out p-3 text-center">
                                Rendi Normale
                            </button>
                        </form>
                    @else
                        <form action="{{ route('article.update', $a->id) }}" method="POST" class="mx-3 inline">
                        @csrf
                            <button type="submit" class="bg-green-600 text-white rounded hover:text-azure-600 hover:bg-gray-400 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-outn p-3 text-center">
                                Rendi Moca
                            </button>
                        </form>
                    @endif
                    <form action="{{ route('article.delete', $a->id) }}" method="POST" class="mx-3 inline">
                    @csrf
                        <button type="submit" class="bg-red-600 text-white rounded hover:text-azure-600 hover:bg-gray-400 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out p-3 text-center">
                            Elimina
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr class="table w-full table-fixed">
                <td colspan="4" class="text-center p-5">Nessun articolo trovato</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @else
    <div class="flex justify-between items-center border-b border-gray-300 pb-2 mb-4">
        <div>
            <p class="text-sm text-white uppercase">CREA NUOVO articolo</p>
        </div>
        <div class="text-right">
            <img src="/images/logo_server_2.png" alt="Logo Onn Water" class="h-12 object-contain">
        </div>
    </div>
    <h2 class="text-xl font-bold text-center mb-4 border-y py-2 bg-blue-50 uppercase">CREA NUOVO articolo</h2>
    <form action="{{ route('article.create') }}" class="space-y-4 w-[40vw] flex flex-col mx-auto" method="POST">
        @csrf
            <label for="code" class="block text-sm font-medium text-gray-900">Codice</label>
            <input type="text" id="code" name="code" class="mt-1 w-full block border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
            <label for="description" class="block text-sm font-medium text-gray-900">Descrizione</label>
            <input type="text" id="description" name="description" class="mt-1 w-full block border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
            <label for="padre_description" class="block text-sm font-medium text-gray-900">Descrizione Padre</label>
            <input type="text" id="padre_description" name="padre_description" class="mt-1 w-full block border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
            <div class="w-fit">
                <label for="is_moca" class="block text-sm font-medium text-gray-900">E' moca ?</label>
                <input type="checkbox" class="mt-1 block border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" name="is_moca" id="is_moca">
            </div>
        <button class="bg-green-600 text-white font-semibold py-2 px-4 rounded hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-opacity-75" type="submit">salva</button>
    </form>
    @endif
    <div class="mt-4 w-full h-max flex justify-start">
        <a href="{{ Auth::user()->role == 'Admin' ? route('admin.index') : route('operator.index') }}" class="bg-red-800 text-white font-semibold py-2 px-4 rounded hover:text-azure-600 hover:bg-gray-400 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
            Torna alla home
        </a>
        <button wire:click="toggleTable()" class="bg-blue-600 text-white font-semibold py-2 px-4 rounded hover:text-azure-600 hover:bg-gray-400 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out ms-5">
            {{ $table ? __('Crea nuovo articolo') : __('Visualizza articoli') }}
        </button>
    </div>
</div>
