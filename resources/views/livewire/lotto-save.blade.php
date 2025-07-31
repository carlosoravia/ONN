<div class="max-w-6xl mx-auto border border-gray-900 p-4 rounded shadow m-5">
    <form wire:submit.prevent="submit">
        <input type="hidden" wire:model.defer="pre_assembled_id" />
        <input type="hidden" wire:model.defer="code_lotto" />
        <div class="flex justify-between items-center border-b border-gray-900 pb-2 mb-4 text-white">
            <div>
                <p class="text-sm">TRACCIABILITÀ LOTTI COMPONENTI (PRE-ASSEMBLATI)</p>
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
                    <input id="numberLotto" value="{{$lotto->quantity}}" wire:model.defer="quantity" type="text" class="border border-gray-400 focus:text-dark text-dark bg-white px-3 py-1 mt-1 w-32 rounded">
                @else
                    <input id="numberLotto" wire:model.defer="quantity" type="text" class="border border-gray-400 focus:text-dark text-dark bg-white px-3 py-1 mt-1 w-32 rounded">
                @endif
            </div>
        </div>
        <table class="w-full border border-gray-900 text-sm text-white">
            <thead class="bg-gray-200 text-left">
                <tr class="table w-full table-fixed text-white">
                    <th class="border border-gray-900 px-2 py-1">ARTICOLO UTILIZZATO<br><span class="text-xs font-normal">(componenti necessari per l'assemblaggio)</span></th>
                    <th class="border border-gray-900 px-2 py-1">CODICE ARTICOLO<br><span class="text-xs">ONN WATER</span></th>
                    <th class="border border-gray-900 px-2 py-1">LOTTO ARTICOLO FORNITORE UTILIZZATO</th>
                </tr>
            </thead>
            <tbody class="block max-h-[40vh] overflow-y-auto w-full">
                @foreach ($articles as $a)
                <tr class="table w-full table-fixed text-white">
                    <td class="border border-gray-900 px-2 py-1">{{$a->description}}</td>
                    <td class="border border-gray-900 px-2 py-1">
                        <div class="flex items-center justify-between">
                            {{$a->code}}
                            @if($a->is_moca == '1')
                                <img src="/images/moca_article.png" class="h-9 w-9 object-cover rounded shadow-lg" alt="">
                            @endif
                        </div>
                    </td>
                    @if($supplierCodes)
                    <td class="border border-gray-900 px-2 py-1" style="color: black !important; padding: 0px !important;">
                        <input type="hidden" wire:model.defer="components.{{ $loop->index }}.article_id" value="{{ $a->id }}">
                        <input type="text" wire:model.defer="components.{{ $loop->index }}.supplier_code" class="w-full h-full border border-gray-300 rounded bg-gray-500 text-white focus:outline-none focus:ring-2 focus:ring-gray-700 focus:border-gray-500">
                    </td>
                    @else
                    <td class="border border-gray-900 px-2 py-1" style="color: black !important; padding: 0px !important;">
                        <input type="hidden" wire:model.defer="components.{{ $loop->index }}.article_id" value="{{ $a->id }}">
                        <input
                            type="text"
                            wire:model.defer="components.{{ $loop->index }}.supplier_code"
                            class="w-full h-full border border-gray-300 rounded bg-gray-500 text-white focus:outline-none focus:ring-2 focus:ring-gray-700 focus:border-gray-500"
                        />
                        @error('code_lotto')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
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
@if ($errors->any())
    <div id="error-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-lg w-full relative">
            <button onclick="document.getElementById('error-modal').style.display='none'"
                    class="absolute top-2 right-2 text-xl font-bold text-gray-600 hover:text-red-500">
                &times;
            </button>
            <h2 class="text-xl font-semibold mb-4 text-red-600">Si sono verificati errori:</h2>
            <ul class="list-disc list-inside text-sm text-red-500">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
</div>

