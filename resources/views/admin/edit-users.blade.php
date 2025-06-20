<x-app-layout>
    <div class="max-w-6xl h-[80vh] mx-auto border border-gray-900 p-4 rounded shadow overflow-y-auto m-5">
        <div class="flex justify-between items-center border-b border-gray-900 pb-2 mb-4">
            <div>
                <p class="text-sm text-white">LISTA UTENTI</p>
                {{-- <p class="text-sm">Prima emissione del 22/03/2024<br>Rev. 01 del 16/04/2024</p> --}}
            </div>
            <div class="text-right">
                <img src="/images/logo_server_2.png" alt="Logo Onn Water" class="h-12 object-contain">
            </div>
        </div>
        <h2 class="text-xl font-bold text-center mb-4 border-y py-2 bg-blue-50 dark:bg-blue-900 dark:text-blue-200">UTENTI</h2>
        <table class="w-full h-max-content border border-gray-900 text-sm">
            <thead class="bg-gray-200 text-left text-white">
                <tr>
                    <th class="border border-gray-900 px-2 py-1 uppercase">Utente<br><span class="text-xs font-normal">(descrizione del lotto)</span></th>
                    <th class="border border-gray-900 px-2 py-1 uppercase">Matricola</th>
                    <th class="border border-gray-900 px-2 py-1 uppercase">Ruolo</th>
                    <th class="border border-gray-900 px-2 py-1 uppercase">Controlli</th>
                </tr>
            </thead>
            <tbody class="text-white" style="overflow-y: auto; height: ;">
                @foreach ($users as $u)
                <tr>
                    <td class="border border-gray-900 p-5 ">{{$u->name}}</td>
                    <td class="border border-gray-900 px-2 py-1">{{$u->operator_code}}</td>
                    <td class="border border-gray-900 px-2 py-1 {{ $u->role == 'Admin' ? 'text-green-400' : 'text-white' }}">{{$u->role}}</td>
                    <td class="flex justify-between items-center px-3 py-1">
                        <form action="{{ route('user.delete', $u->id) }}" method="POST" class="">
                        @csrf
                            <button type="submit" class="bg-red-600 text-white rounded hover:bg-red-700 transition mx-3 p-3 text-center w-full h-full">
                                Elimina
                            </button>
                        </form>
                        <form action="{{ route('user.makeAdmin', $u->id) }}" method="POST" class="w-29 h-29">
                        @csrf
                            <button type="submit" class="bg-green-600 text-white rounded hover:bg-green-700 transition mx-3 p-3 text-center w-full h-full">
                                Rendi Admin
                            </button>
                        </form>
                        <form action="{{ route('user.makeOperator', $u->id) }}" method="POST" class="w-29 h-29">
                        @csrf
                            <button type="submit" class="bg-blue-600 text-white rounded hover:bg-blue-700 transition mx-3 p-3 text-center w-full h-full">
                                Rendi Operatore
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
