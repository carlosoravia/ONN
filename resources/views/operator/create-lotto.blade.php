<x-app-layout>
    <div class="max-w-6xl mx-auto border border-gray-300 dark:border-gray-600 p-4 rounded shadow">
    <form action="{{ route('lotto.submit') }}" method="POST">
        @csrf
        <input type="hidden" name="pre_assembled_id" value="{{ $preAssembled->id }}">
        <input type="hidden" name="code_lotto" value="{{ $lottoCode }}">
        <div class="flex justify-between items-center border-b border-gray-300 dark:border-gray-600 pb-2 mb-4 text-white">
            <div>
                <p class="text-sm">IO 05 – TRACCIABILITÀ LOTTI COMPONENTI (PRE-ASSEMBLATI)</p>
                {{-- <p class="text-sm">Prima emissione del 22/03/2024<br>Rev. 01 del 16/04/2024</p> --}}
            </div>
            <div class="text-right">
                <img src="/images/logo-white.png" alt="Logo Onn Water" class="h-12 object-contain">
            </div>
        </div>
        <h2 class="text-xl font-bold text-center mb-4 border-y py-2 bg-blue-50 dark:bg-blue-900 dark:text-blue-200">PRE-ASSEMBLATI</h2>
        <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-red-600 dark:text-red-400 font-semibold">CODICE PRE-ASSEMBLATO: <span class="text-gray-600 dark:text-gray-400">{{$preAssembled->code}}</span></p>
                <p class="text-red-600 dark:text-red-400">
                    LOTTO N°:
                    <span class="text-gray-600 dark:text-gray-400">{{$lottoCode}}</span>
                </p>
            </div>
            <div class="flex flex-col items-end">
                <label for="numberLotto" class="text-red-600 dark:text-red-400 font-semibold">N° PZ LOTTO:</label>
                @if(!empty($lotto->quantity))
                    <input id="numberLotto" value="{{$lotto->quantity}}" name="quantity" type="text" class="border border-gray-400 focus:text-white text-white dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-1 mt-1 w-32 rounded">
                @else
                    <input id="numberLotto" name="quantity" type="text" class="border border-gray-400 focus:text-white text-white dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-1 mt-1 w-32 rounded">
                @endif
            </div>
        </div>
        <table class="w-full border border-gray-400 dark:border-gray-600 text-sm text-white">
            <thead class="bg-gray-200 dark:bg-gray-700 text-left">
                <tr>
                    <th class="border border-gray-400 dark:border-gray-600 px-2 py-1">ARTICOLO UTILIZZATO<br><span class="text-xs font-normal">(componenti necessari per l'assemblaggio)</span></th>
                    <th class="border border-gray-400 dark:border-gray-600 px-2 py-1">CODICE ARTICOLO<br><span class="text-xs">ONN WATER</span></th>
                    <th class="border border-gray-400 dark:border-gray-600 px-2 py-1">LOTTO ARTICOLO FORNITORE UTILIZZATO</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($articles as $a)
                <tr>
                    <td class="border border-gray-300 dark:border-gray-600 px-2 py-1">{{$a->description}}</td>
                    <td class="border border-gray-300 dark:border-gray-600 px-2 py-1">{{$a->code}}</td>
                    {{-- Check if supplierCodes is set and has the same number of elements as articles --}}
                    @if($supplierCodes)
                    <td class="border border-gray-300 dark:border-gray-600">
                        <input type="hidden" name="components[{{ $loop->index }}][article_id]" value="{{ $a->id }}">
                        <input type="text" name="components[{{ $loop->index }}][supplier_code]" value="{{$components[$loop->index]['supplier_code']}}" class="w-full h-full default:bg-dark-700">
                    </td>
                    @else
                    <td class="border border-gray-300 dark:border-gray-600">
                        <input type="hidden" name="components[{{ $loop->index }}][article_id]" value="{{ $a->id }}">
                        <input type="text" name="components[{{ $loop->index }}][supplier_code]" class="w-full h-full default:bg-dark-700" placeholder="Codice fornitore">
                    </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4 w-full h-max flex justify-center">
            <button type="submit" class="bg-blue-800 text-white font-semibold py-2 px-4 rounded hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-opacity-75">
                Inserisci
            </button>
        </div>
    </form>
</div>

</x-app-layout>
