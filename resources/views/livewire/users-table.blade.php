<div class="max-w-6xl h-fit mx-auto my-0 border border-gray-900 p-4 rounded shadow mt-5">
    @if($table)
    <div class="flex justify-between items-center border-b border-gray-300 pb-2 mb-4">
        <div>
            <p class="text-sm text-white">LISTA UTENTI</p>
        </div>
        <div class="text-right">
            <img src="/images/logo_server_2.png" alt="Logo Onn Water" class="h-12 object-contain">
        </div>
    </div>
    <h2 class="text-xl font-bold text-center mb-4 border-y py-2 bg-blue-50">UTENTI</h2>
    <input wire:model.live="query" placeholder="Cerca Per Nome..." class="border border-gray-400 focus:text-dark text-dark bg-white px-3 py-1 mt-1 rounded mb-4">
    <input wire:model.live="queryNumber" placeholder="Cerca Per Codice..." class="border border-gray-400 focus:text-dark text-dark bg-white px-3 py-1 mt-1 rounded mb-4">
    <table class="w-full h-max-content border border-gray-900 text-sm">
        <thead class="bg-gray-200 text-left text-white">
            <tr class="table w-full table-fixed">
                <th class="border border-gray-900 px-2 py-1 uppercase w-1/4">Utente<br><span class="text-xs font-normal">(descrizione del lotto)</span></th>
                <th class="border border-gray-900 px-2 py-1 uppercase w-1/4">Matricola</th>
                <th class="border border-gray-900 px-2 py-1 uppercase w-1/4">Ruolo</th>
                <th class="border border-gray-900 px-2 py-1 uppercase w-1/4">Controlli</th>
            </tr>
        </thead>
        <tbody class="block max-h-[40vh] overflow-y-auto w-full text-white">
            @foreach ($users as $u)
            <tr class="table w-full table-fixed">
                <td class="border border-gray-900 w-1/4 p-5 ">{{$u->name}}</td>
                <td class="border border-gray-900 w-1/4 px-2 py-1">{{$u->operator_code}}</td>
                <td class="border border-gray-900 w-1/4 px-2 py-1 {{ $u->role == 'Admin' ? 'text-green-400' : 'text-white' }}">{{$u->role}}</td>
                <td class="grid grid-cols-2 place-items-center px-3 py-1 w-fit">
                    @if ($u->role === 'Admin')
                        <form action="{{ route('user.makeOperator', $u->id) }}" method="POST" class="mx-3 inline">
                        @csrf
                            <button type="submit" class="bg-blue-600 text-white rounded hover:text-azure-600 hover:bg-gray-400 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out p-3 text-center">
                                Rendi Operatore
                            </button>
                        </form>
                    @else
                        <form action="{{ route('user.makeAdmin', $u->id) }}" method="POST" class="mx-3 inline">
                        @csrf
                            <button type="submit" class="bg-green-600 text-white rounded hover:text-azure-600 hover:bg-gray-400 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-outn p-3 text-center">
                                Rendi Admin
                            </button>
                        </form>
                    @endif
                    <form action="{{ route('user.delete', $u->id) }}" method="POST" class="mx-3 inline">
                    @csrf
                        <button type="submit" class="bg-red-600 text-white rounded hover:text-azure-600 hover:bg-gray-400 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out p-3 text-center">
                            Elimina
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="flex justify-between items-center border-b border-gray-300 pb-2 mb-4">
        <div>
            <p class="text-sm text-white">CREA NUOVO UTENTE</p>
        </div>
        <div class="text-right">
            <img src="/images/logo_server_2.png" alt="Logo Onn Water" class="h-12 object-contain">
        </div>
    </div>
    <h2 class="text-xl font-bold text-center mb-4 border-y py-2 bg-blue-50">CREA NUOVO UTENTE</h2>
    <form action="{{ route('admin.createUser') }}" class="space-y-4 w-fit flex flex-col mx-auto" method="POST">
        @csrf
        <div class="flex justify-between w-full">
            <div class="w-[48%]">
                <label for="name" class="block text-sm font-medium text-gray-900">Nome</label>
                <input type="text" id="name" name="name" class="mt-1 w-full block border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
            </div>
            <div class="w-[48%]">
                <label for="surname" class="block text-sm font-medium text-gray-900">Cognome</label>
                <input type="text" id="surname" name="surname" class="mt-1 w-full block border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
            </div>
        </div>
        <div>
            <label for="operator_code" class="block text-sm font-medium text-gray-900">Codice Operatore</label>
            <input type="text" id="operator_code" name="operator_code" class="mt-1 w-full block border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-gray-900">Password Utente</label>
            <input type="text" id="password" name="password" class="mt-1 w-full block border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
        </div>
        <div>
            <label for="role" class="block text-sm font-medium text-gray-900">Ruolo Utente</label>
            <select class="mt-1 w-full block border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" name="role" id="role" required>
                <option value="admin">Admin</option>
                <option value="operator">Operatore</option>
            </select>
        </div>
        <button class="bg-green-600 text-white font-semibold py-2 px-4 rounded hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-opacity-75" type="submit">salva</button>
    </form>
    @endif
    <div class="mt-4 w-full h-max flex justify-start">
        <a href="{{ Auth::user()->role == 'Admin' ? route('admin.index') : route('operator.index') }}" class="bg-red-800 text-white font-semibold py-2 px-4 rounded hover:text-azure-600 hover:bg-gray-400 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
            Torna alla home
        </a>
        <button wire:click="toggleTable()" class="bg-blue-600 text-white font-semibold py-2 px-4 rounded hover:text-azure-600 hover:bg-gray-400 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out ms-5">
            {{ $table ? __('Crea nuovo utente') : __('Visualizza utenti') }}
        </button>
    </div>
</div>
