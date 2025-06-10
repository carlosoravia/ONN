<x-app-layout>
    <div class="max-w-6xl h-fit mx-auto my-0 border border-gray-300 p-4 rounded shadow m-5">
        <form wire:submit.prevent="submit">
            <div class="flex justify-between items-center border-b border-gray-300 pb-2 mb-4">
                <div>
                    <p class="text-sm text-white">LISTA PRE-ASSEMBLATI</p>
                    {{-- <p class="text-sm">Prima emissione del 22/03/2024<br>Rev. 01 del 16/04/2024</p> --}}
                </div>
                <div class="text-right">
                    <img src="/images/logo-white.png" alt="Logo Onn Water" class="h-12 object-contain">
                </div>
            </div>
            <h2 class="text-xl font-bold text-center mb-4 border-y py-2 bg-gray-900">PRE-ASSEMBLATI</h2>
            <table class="w-full h-max-content border border-gray-400 text-sm">
                <thead class="bg-gray-200 text-left block w-full">
                    <tr class="table w-full table-fixed text-white">
                        <th class="border border-gray-400 px-2 py-1 uppercase w-2/4">descrizione<br><span class="text-xs font-normal">(componenti necessari per l'assemblaggio)</span></th>
                        <th class="border border-gray-400 px-2 py-1 uppercase w-1/4">codice pre-assemblato<br><span class="text-xs">ONN WATER</span></th>
                        <th class="border border-gray-400 px-2 py-1 uppercase w-1/4"></th>
                    </tr>
                </thead>
                <tbody class="block max-h-[55vh] overflow-y-auto w-full">
                    @foreach ($preassembleds as $p)
                    <tr class="table w-full table-fixed text-white">
                        <td class="border border-gray-300 w-2/4 p-5 ">{{$p->padre_description}}</td>
                        <td class="border border-gray-300 w-1/4 px-2 py-1">{{$p->code}}</td>
                        <td class="border border-gray-300 w-1/4 p-0">
                            <div class="flex justify-center items-center h-full px-3">
                                <a href="{{route('lotto.create', $p->id)}}" class="bg-blue-600 text-white rounded hover:bg-blue-700 transition p-3 w-full h-full text-center">
                                    Seleziona
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </form>
        <div class="mt-4 w-full h-max flex justify-start">
            <a href="{{ Auth::user()->role ? route('admin.index') : route('operator.index') }}" class="bg-red-800 text-white font-semibold py-2 px-4 rounded hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-opacity-75">
                Torna alla home
            </a>
        </div>
    </div>
</x-app-layout>
