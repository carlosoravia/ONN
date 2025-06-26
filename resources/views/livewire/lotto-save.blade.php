<div class="max-w-6xl mx-auto border border-gray-900 p-4 rounded shadow mt-12">
    <form wire:submit.prevent="submit">
        <input type="hidden" wire:model="pre_assembled_id" />
        <input type="hidden" wire:model="code_lotto" />
        <div class="flex justify-between items-center border-b border-gray-900 pb-2 mb-4 text-white">
            <div>
                <p class="text-sm">IO 05 – TRACCIABILITÀ LOTTI COMPONENTI (PRE-ASSEMBLATI)</p>
            </div>
            <div class="text-right">
                <img src="/images/logo_server_2.png" alt="Logo Onn Water" class="h-12 object-contain">
            </div>
        </div>
        <h2 class="text-xl font-bold text-center mb-4 border-y py-2 bg-blue-50">PRE-ASSEMBLATI</h2>
        <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-azure-500 font-semibold">CODICE PRE-ASSEMBLATO: <span class="text-white">{{$preAssembled->code}}</span></p>
                <p class="text-azure-500">
                    LOTTO N°:
                    <span class="text-white">{{$lottoCode}}</span>
                </p>
            </div>
            <div class="flex flex-col items-end">
                <label for="numberLotto" class="text-azure-500 font-semibold">N° PZ LOTTO:</label>
                @if(!empty($lotto->quantity))
                    <input id="numberLotto" value="{{$lotto->quantity}}" wire:model="quantity" type="text" class="border border-gray-400 focus:text-dark text-dark bg-white px-3 py-1 mt-1 w-32 rounded">
                @else
                    <input id="numberLotto" wire:model="quantity" type="text" class="border border-gray-400 focus:text-dark text-dark bg-white px-3 py-1 mt-1 w-32 rounded">
                @endif
            </div>
        </div>
        <table class="w-full border border-gray-900 text-sm text-white">
            <thead class="bg-gray-200 text-left">
                <tr>
                    <th class="border border-gray-900 px-2 py-1">ARTICOLO UTILIZZATO<br><span class="text-xs font-normal">(componenti necessari per l'assemblaggio)</span></th>
                    <th class="border border-gray-900 px-2 py-1">CODICE ARTICOLO<br><span class="text-xs">ONN WATER</span></th>
                    <th class="border border-gray-900 px-2 py-1">LOTTO ARTICOLO FORNITORE UTILIZZATO</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($articles as $a)
                <tr>
                    <td class="border border-gray-900 px-2 py-1">{{$a->description}}</td>
                    <td class="border border-gray-900 px-2 py-1">{{$a->code}}</td>
                    {{-- Check if supplierCodes is set and has the same number of elements as articles --}}
                    @if($supplierCodes)
                    <td class="border border-gray-900" style="color: black !important;">
                        <input type="hidden" wire:model="components.{{ $loop->index }}.article_id" value="{{ $a->id }}">
                        <input type="text" wire:model="components.{{ $loop->index }}.supplier_code" class="w-full h-full">
                    </td>
                    @else
                    <td class="border border-gray-900" style="color: black !important;">
                        <input type="hidden" wire:model="components.{{ $loop->index }}.article_id" value="{{ $a->id }}">
                        <input type="text" wire:model="components.{{ $loop->index }}.supplier_code" class="w-full h-full" placeholder="Codice fornitore">
                    </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4 w-full h-max flex justify-center">
            <x-primary-button type="submit">
                Procedi
            </x-primary-button>
        </div>
    </form>
</div>
