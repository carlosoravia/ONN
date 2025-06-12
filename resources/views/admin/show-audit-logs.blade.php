<x-app-layout>
    <div class="max-w-4xl mx-auto p-6 text-xl">
        <h2 class="font-bold text-azure-400 mb-4">Dettaglio Audit Log: {{ ucfirst($audit->action) }}</h2>

        <div class="bg-gray-100 rounded-lg shadow p-6 border border-gray-300">
            <ul class="space-y-3 text-gray-800">
                <li><strong>Data:</strong> {{ $audit->created_at->format('d/m/Y H:i:s') }}</li>
                <li><strong>Utente:</strong> {{ $user->name ?? 'N/A' }}</li>
                <li><strong>Azione:</strong> {{ $audit->action }}</li>
                <li><strong>Edita: </strong><a href="{{ $linkToRecord }}" class="text-cyan-400 hover:text-cyan-200">{{ $lottoCode }}</a></li>
                <li><strong>Scarica PDF: </strong><a href="{{ route('download.lotto', ['filename' => $lottoCode . '.pdf']) }}" class="text-cyan-400 hover:text-cyan-200">Scarica qui</a></li>
            </ul>
            @if (!empty($updatedData))
                <div class="mt-8">
                    <h3 class="text-lg font-semibold text-azure-500 mb-2">
                        Dati {{ $audit->action == 'created' ? 'creati' : ($audit->action == 'deleted' ? 'eliminati' : 'modificati') }}
                    </h3>
                    <table class="w-full text-left text-sm bg-white rounded border border-gray-300">
                        <thead class="bg-gray-800 text-white">
                            <tr>
                                <th class="px-4 py-2">Campo</th>
                                @if($audit->action !== 'created')
                                    <th class="px-4 py-2">Valore Precedente</th>
                                @endif
                                @if($audit->action !== 'deleted')
                                    <th class="px-4 py-2">Valore Nuovo</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                        @if (isset($updatedData[0]) && is_array($updatedData[0]) && isset($updatedData[1]))
                            {{-- Struttura a due array paralleli (solo update) --}}
                            @for ($i = 0; $i < count($updatedData[0]); $i++)
                                @php
                                    $old = $updatedData[0][$i] ?? null;
                                    $new = $updatedData[1][$i] ?? null;
                                @endphp
                                @if ($old !== $new)
                                <tr class="border-t text-lg font-semibold">
                                    <td class="px-4 py-2 font-medium">Campo {{ $i + 1 }}</td>
                                    @if ($audit->action !== 'created')
                                        <td class="px-4 py-2 text-red-600">{{ $old ?? 'N/A' }}</td>
                                    @endif
                                    @if ($audit->action !== 'deleted')
                                        <td class="px-4 py-2 text-green-600">{{ $new ?? 'N/A' }}</td>
                                    @endif
                                </tr>
                                @endif
                            @endfor
                        @else
                            @foreach ($updatedData as $campo => $valori)
                                <tr class="border-t text-lg font-semibold">
                                    <td class="px-4 py-2 font-medium">{{ $campo }}</td>
                                    @if ($audit->action !== 'created')
                                        <td class="px-4 py-2 text-red-600">
                                            {{ is_array($valori) && array_key_exists('old', $valori) ? $valori['old'] : '—' }}
                                        </td>
                                    @endif
                                    @if ($audit->action !== 'deleted')
                                        <td class="px-4 py-2 text-green-600">
                                            {{ is_array($valori) && array_key_exists('new', $valori) ? $valori['new'] : (is_array($valori) ? implode(', ', $valori) : $valori) }}
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500">Nessuna modifica registrata.</p>
            @endif
        </div>

        <div class="mt-6">
            <a href="{{ route('admin.index') }}"
               class="bg-red-500 hover:bg-red-400 text-black px-4 py-2 rounded transition text-white hover:text-black">
                ← Torna alla dashboard
            </a>
        </div>
    </div>
</x-app-layout>
