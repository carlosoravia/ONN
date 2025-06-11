<x-app-layout>
    <div class="max-w-4xl mx-auto p-6">
        <h2 class="text-2xl font-bold text-azure-400 mb-4">Dettaglio Audit Log</h2>

        <div class="bg-gray-100 rounded-lg shadow p-6 border border-gray-300">
            <ul class="text-sm space-y-3 text-gray-800">
                <li><strong>Data:</strong> {{ $audit->created_at->format('d/m/Y H:i:s') }}</li>
                <li><strong>Utente:</strong> {{ $user->name ?? 'N/A' }}</li>
                <li><strong>Azione:</strong> {{ $audit->action }}</li>
                <li><strong>Tabella:</strong> {{ $audit->table_name }}</li>
                <li><strong>ID Record:</strong> {{ $audit->record_id }}</li>
            </ul>
            @if (!empty($data))
                <div class="mt-8">
                    <h3 class="text-lg font-semibold text-azure-500 mb-2">Dati modificati</h3>
                    <table class="w-full text-left text-sm bg-white rounded border border-gray-300">
                        <thead class="bg-gray-800">
                            <tr>
                                <th class="px-4 py-2">Campo</th>
                                <th class="px-4 py-2">Valore Precedente</th>
                                <th class="px-4 py-2">Valore Nuovo</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{ $i = 0 }}
                            @foreach ($updatedData[0] as $d)
                                <tr class="border-t">
                                    <td class="px-4 py-2 font-semibold">Codice fornitore</td>
                                    <td class="px-4 py-2 text-red-600">{{ $d }}</td>
                                    <td class="px-4 py-2 text-green-600">{{ $updatedData[1][$i] }}</td>
                                </tr>
                                {{ $i++ }}
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="mt-6">
            <a href="{{ route('admin.index') }}"
               class="bg-azure-500 hover:bg-azure-400 text-black px-4 py-2 rounded transition">
                ← Torna all'elenco
            </a>
        </div>
    </div>
</x-app-layout>
