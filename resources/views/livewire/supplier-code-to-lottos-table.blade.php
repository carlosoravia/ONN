<div class="p-6 max-w-xl mx-auto bg-white shadow rounded">
    <h2 class="text-xl font-bold mb-4">Cerca lotti per codice articolo</h2>

    @if (session()->has('error'))
        <div class="text-red-600 mb-3">{{ session('error') }}</div>
    @endif

    <div class="flex gap-2">
        <input type="text" wire:model.defer="supplier_code" placeholder="Es: ART123"
            class="w-full border border-gray-300 rounded px-4 py-2">
        <button wire:click="search"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Cerca</button>
    </div>

    @if (!empty($lottos))
        <div class="mt-6">
            <h3 class="font-semibold text-md mb-2">Lotti trovati:</h3>
            <ul class="list-disc ml-6 space-y-1">
                @foreach ($lottos as $lotto)
                    <li>
                        <strong>{{ $lotto->code_lotto }}</strong>
                        @if ($lotto->created_at)
                            <span class="text-sm text-gray-500">({{ $lotto->created_at->format('d/m/Y') }})</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
