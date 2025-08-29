<div class="max-w-6xl h-fit mx-auto my-0 border border-gray-900 p-4 rounded shadow my-5">
    <form class="space-y-4 flex flex-col mx-auto justify-center">
        <div class="bg-gray-900 w-full rounded p-3 overflow-y-auto max-h-[40vh]">
            <h2 class="text-2xl p-2 font-bold">Elimina componenti pre-assemblato</h2>
            <table class="table-auto w-full">
                <thead>
                    <tr class="w-fit">
                        <th class="px-4 py-2 text-left text-sm font-medium border border-black-900 text-start p-2 w-1/4">Codice</th>
                        <th class="px-4 py-2 text-left text-sm font-medium border border-black-900 text-start p-2 w-1/2">Descrizione</th>
                        <th class="px-4 py-2 text-left text-sm font-medium border border-black-900 text-start p-2 w-1/4">Seleziona</th>
                    </tr>
                </thead>
                <tbody class="">
                    @foreach ($articles as $a)
                        <tr class="w-fit" wire:key="article-{{ $a->id }}">
                            <th class="text-sm border border-black-900 text-start p-2 w-1/4">{{ $a->code }}</th>
                            <th class="text-sm border border-black-900 text-start p-2 w-1/2">{{ $a->description }}</th>
                            <th class="border border-black-900 text-start p-2 w-1/4">
                                <button type="button" class="bg-red-600 text-white p-3 border border-black-900 rounded hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-300 focus:ring-opacity-75" wire:click="removeArticle({{ $a->id }})">
                                    Rimuovi
                                </button>
                            </th>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </form>
    <div class="mt-4 w-full h-max flex justify-start">
        <a href="{{ Auth::user()->role == 'Admin' ? route('admin.index') : route('operator.index') }}" class="bg-red-800 text-white font-semibold py-2 px-4 rounded hover:text-azure-600 hover:bg-gray-400 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
            Torna alla home
        </a>
    </div>
</div>
