<div class="max-w-6xl h-fit mx-auto my-0 border border-gray-900 p-4 rounded shadow my-5">
    <button wire:click="toggleTable()" class="bg-blue-600 text-white font-semibold py-2 px-4 rounded hover:text-azure-600 hover:bg-gray-400 transition duration-150 ease-in-out">
        {{ $table ? __('Edita preassemblati') : __('Crea preassemblato') }}
    </button>
    <div class="flex justify-between items-center border-b border-gray-300 pb-2 mb-4">
        <div>
            <p class="text-sm text-white uppercase">{{ $table ? __('Crea preassemblato') : __('Edita preassemblati') }}</p>
        </div>
        <div class="text-right">
            <img src="/images/logo_server_2.png" alt="Logo Onn Water" class="h-12 object-contain">
        </div>
    </div>
    <h2 class="text-xl font-bold text-center mb-4 border-y py-2 bg-blue-50 uppercase">{{ $table ? __('Crea preassemblato') : __('Edita preassemblati') }}</h2>
    @if($table)
    <form wire:submit.prevent="submit" class="space-y-4 flex flex-col mx-auto justify-center">
        <div class="w-[35vw]">
            <label for="preassembled_code" class="block text-sm font-medium text-gray-900">Codice preassemblato</label>
            <input type="text" id="preassembled_code" wire:model.defer="preassembled_code" class="mt-1 w-full block border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
        </div>
        <div class="w-[35vw]">
            <label for="preassembled_description" class="block text-sm font-medium text-gray-900">Descrizione preassemblato</label>
            <textarea rows="4" id="preassembled_description" wire:model.defer="preassembled_description" class="mt-1 w-full block border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required></textarea>
        </div>
        <div class="bg-gray-900 w-full rounded p-3 overflow-y-auto max-h-[29vh]">
            <h2 class="text-2xl p-2 font-bold">Lista componenti pre-assemblato</h2>
            <table class="table-auto w-full">
                <thead>
                    <tr class="w-fit">
                        <th class="px-4 py-2 text-left text-sm font-medium border border-black-900 text-start p-2 w-1/4">Codice</th>
                        <th class="px-4 py-2 text-left text-sm font-medium border border-black-900 text-start p-2 w-1/2">Descrizione</th>
                        <th class="px-4 py-2 text-left text-sm font-medium border border-black-900 text-start p-2 w-1/4">Deseleziona</th>
                    </tr>
                </thead>
                <tbody class="">
                    @forelse ($selectedArticles as $sa)
                        <tr class="w-fit">
                            <th class="text-sm border border-black-900 text-start p-2 w-1/4">{{ $sa->code }}</th>
                            <th class="text-sm border border-black-900 text-start p-2 w-1/2">{{ $sa->description }}</th>
                            <th class="border border-black-900 text-start p-2 w-1/4">
                                <button class="bg-red-600 text-white p-3 border border-black-900 rounded hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-300 focus:ring-opacity-75" wire:click="removeArticle({{ $sa->id }})">
                                    rimuovi
                                </button>
                            </th>
                        </tr>
                    @empty
                    <tr>
                        <th class="w-full bg-blue-800">
                            <p class="p-2 text-2xl">Seleziona gli articoli da inserire</p>
                        </th>
                        <th class="bg-blue-800"></th>
                        <th class="bg-blue-800"></th>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-gray-900 w-full rounded p-3 overflow-y-auto max-h-[29vh]">
            <h2 class="text-2xl p-2 font-bold">Selezione componenti pre-assemblato</h2>
            <input type="text" wire:model.live="query" placeholder="Cerca per codice..." class="mb-3 p-2 rounded border border-gray-300 focus:border-blue-500 focus:ring-blue-500">
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
                                <button type="button" class="bg-green-600 text-white p-3 border border-black-900 rounded hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-green-300 focus:ring-opacity-75" wire:click="addArticle({{ $a->id }})">
                                    Aggiungi
                                </button>
                            </th>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <button class="bg-green-600 text-white font-semibold py-2 px-4 rounded hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-opacity-75" type="submit">salva</button>
    </form>
    @else
        <livewire:preassembled-table :context="'admin'"/>
    @endif
</div>
