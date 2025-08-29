<div class="max-w-6xl h-fit mx-auto my-0 border border-gray-900 p-4 rounded shadow my-5 bg-gray2-900">
    <div class="flex justify-between items-center pb-2 mb-4">
        <button wire:click="toggleTable()" class="bg-blue-600 text-white font-semibold py-2 px-4 rounded hover:text-azure-600 hover:bg-gray-400 transition duration-150 ease-in-out">
            {{ $table ? __('Edita preassemblati') : __('Crea preassemblato') }}
        </button>
        <div class="text-right">
            <img src="/images/logo_server_1.png" alt="Logo Onn Water" class="h-12 object-contain">
        </div>
    </div>
    <h2 class="text-xl font-bold text-center mb-4 border-y py-2 bg-black-500 uppercase">{{ $table ? __('Crea preassemblato') : __('Edita preassemblati') }}</h2>
    @if($table)
    <form wire:submit.prevent="submit" class="space-y-4 flex flex-col mx-auto justify-center">
        <div class="w-[35vw]">
            <label for="preassembled_code" class="block text-sm font-medium text-black-900">Codice preassemblato</label>
            <input type="text" id="preassembled_code" wire:model.defer="preassembled_code" class="mt-1 w-full block border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500 sm:text-sm shadow-lg" required>
        </div>
        <div class="w-[35vw]">
            <label for="preassembled_description" class="block text-sm font-medium text-black-900">Descrizione preassemblato</label>
            <textarea rows="4" id="preassembled_description" wire:model.defer="preassembled_description" class="mt-1 w-full block border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500 sm:text-sm shadow-lg" required></textarea>
        </div>
        <div class="overflow-x-auto bg-gray2-900 rounded p-3 shadow-lg border-2 border-gray-700">
            <h2 class="text-2xl p-2 font-bold">Lista componenti pre-assemblato</h2>
            <div class="max-h-[60vh] overflow-y-auto bg-gray-900 rounded">
                <table class="table w-full">
                    <thead class="sticky top-0 bg-gray-200 z-10 text-white">
                        <tr>
                            <th class="w-1/4 px-4 py-2 border text-start">Codice</th>
                            <th class="w-1/2 px-4 py-2 border text-start">Descrizione</th>
                            <th class="w-1/4 px-4 py-2 border text-start">Deseleziona</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-300">
                        @forelse ($selectedArticles as $sa)
                            <tr wire:key="selected-{{ $sa->id }}" class="{{ $loop->index % 2 == 0 ? 'bg-gray-800' : 'bg-gray-900' }}">
                                <th class="text-sm border border-black-900 text-start p-2 w-1/4">{{ $sa->code }}</th>
                                <th class="text-sm border border-black-900 text-start p-2 w-1/2">{{ $sa->description }}</th>
                                <th class="border border-black-900 text-start p-2 w-1/4">
                                    <button type="button" class="bg-red-600 text-white p-3 border border-black-900 rounded hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-300 focus:ring-opacity-75" wire:click="removeArticle({{ $sa->id }})">
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
        </div>
        <div class="overflow-x-auto bg-gray2-900 rounded p-3 shadow-lg border-2 border-gray-700">
            <h2 class="text-2xl p-2 font-bold">Selezione componenti pre-assemblato</h2>
            <input type="text" wire:model.live="query" placeholder="Cerca per codice..." class="mb-3 p-2 rounded border border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            <div class="max-h-[60vh] overflow-y-auto bg-gray-900 rounded">
                <table class="table w-full text-black-900">
                    <thead class="sticky top-0 bg-gray-200 z-10 text-white">
                        <tr>
                            <th class="w-1/4 px-4 py-2 border text-start">Codice</th>
                            <th class="w-1/2 px-4 py-2 border text-start">Descrizione</th>
                            <th class="w-1/4 px-4 py-2 border text-start">Seleziona</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-300">
                        @foreach ($articles as $a)
                        <tr wire:key="article-{{ $a->id }}" class="{{ $loop->index % 2 == 0 ? 'bg-gray-800' : 'bg-gray-900' }}">
                            <td class="w-1/4 px-4 py-2 border text-start">{{ $a->code }}</td>
                            <td class="w-1/2 px-4 py-2 border text-start">{{ $a->description }}</td>
                            <td class="w-1/4 px-4 py-2 border text-start">
                                <button
                                    type="button"
                                    class="bg-green-600 hover:bg-green-500 text-white px-3 py-1 rounded"
                                    wire:click="addArticle({{ $a->id }})">
                                    Aggiungi
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @if($this->canSubmit)
            <button class="bg-green-600 text-white font-semibold py-2 px-4 rounded hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-opacity-75" type="submit" wire:loading.attr="disabled">Carica materiale</button>
        @endif
    </form>
    @else
        <livewire:preassembled-table :context="'admin'"/>
    @endif
    @if($showConfirmModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:keydown.escape="$set('showConfirmModal', false)">
        <div class="bg-white rounded shadow p-6 w-full max-w-md" wire:ignore.self>
            <h3 class="text-lg font-semibold mb-2">Confermi la creazione del pre-assemblato?</h3>
            <p class="text-sm text-gray-700 mb-4">
                Codice: <strong>{{ $preassembled_code }}</strong><br>
                Descrizione: <span class="text-gray-600">{{ $preassembled_description }}</span>
            </p>
            <form action="{{ route('admin.preassembled.store') }}" method="POST" class="flex justify-end gap-3">
                @csrf
                <input type="hidden" name="preassembled_code" value="{{ $preassembled_code }}">
                <input type="hidden" name="preassembled_description" value="{{ $preassembled_description }}">
                @foreach($selectedArticles as $sa)
                    <input type="hidden" name="selected_articles[]" value="{{ $sa->id }}">
                @endforeach
                <button type="button" class="px-4 py-2 rounded bg-red-200 hover:bg-red-300" wire:click="cancelSubmit">Annulla</button>
                <button type="submit" class="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-500">Conferma</button>
            </form>
        </div>
    </div>
    @endif
</div>
