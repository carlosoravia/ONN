<div class="p-6 space-y-6">

    {{-- Header + Navigazione temporale --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold">Pianificazione Ordini</h1>
            <p class="text-sm text-gray-500">
                Storico 3 mesi • Orizzonte 12 mesi • Settimane custom continue
            </p>
        </div>

        <div class="flex items-center gap-2">
            <button wire:click="prevMonth" class="px-3 py-2 rounded-lg border border-gray-200 hover:bg-gray-50">←</button>

            <select wire:model.live="currentMonth"
                    wire:change="$set('week', null); $set('weeksForMonth', computeWeeksForMonth($event.target.value))"
                    class="px-3 py-2 rounded-lg border-gray-300 text-sm">
                @foreach($months as $m)
                    <option value="{{ $m['key'] }}">{{ $m['label'] }}</option>
                @endforeach
            </select>

            <button wire:click="nextMonth" class="px-3 py-2 rounded-lg border border-gray-200 hover:bg-gray-50">→</button>

            <button wire:click="goCurrent" class="ml-2 px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm">
                Oggi
            </button>
        </div>
    </div>

    {{-- Tabs Linee --}}
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex flex-wrap gap-4">
            @foreach (['domestica'=>'Linea Domestica','ufficio'=>'Linea Ufficio','sprint'=>'Linea Sprint','quantitativa'=>'Linea Quantitativa'] as $key=>$label)
                <button wire:click="setLine('{{ $key }}')"
                        class="pb-3 px-2 border-b-2 text-sm font-medium
                               {{ $line === $key ? 'border-blue-600 text-blue-700' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    {{ $label }}
                </button>
            @endforeach
        </nav>
    </div>

    {{-- Chips settimane del mese --}}
    <div class="flex flex-wrap gap-2">
        <button wire:click="selectWeek(null)"
                class="px-3 py-1.5 rounded-full text-sm border
                       {{ $week===null ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
            Tutte
        </button>
        @foreach($weeksForMonth as $w)
            <button wire:click="selectWeek({{ $w }})"
                    class="px-3 py-1.5 rounded-full text-sm border
                           {{ $week===$w ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                Settimana {{ $w }}
            </button>
        @endforeach
    </div>

    {{-- KPI --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-white ring-1 ring-gray-100 shadow-sm">
            <div class="text-xs text-gray-500">Ordini selezionati</div>
            <div class="text-xl font-semibold">{{ $this->kpis['cnt'] }}</div>
        </div>
        <div class="p-4 rounded-2xl bg-white ring-1 ring-gray-100 shadow-sm">
            <div class="text-xs text-gray-500">Totale Q.tà</div>
            <div class="text-xl font-semibold">{{ $this->kpis['qty'] }}</div>
        </div>
        <div class="p-4 rounded-2xl bg-white ring-1 ring-gray-100 shadow-sm">
            <div class="text-xs text-gray-500">Totale</div>
            <div class="text-xl font-semibold">{{ number_format($this->kpis['total'], 0, ',', '.') }}</div>
        </div>
        <div class="p-4 rounded-2xl bg-white ring-1 ring-gray-100 shadow-sm">
            <div class="text-xs text-gray-500">Linea</div>
            <div class="text-sm font-medium">
                <span class="px-2 py-1 rounded-lg
                    @switch($line)
                        @case('domestica')   bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200 @break
                        @case('ufficio')     bg-sky-50 text-sky-700 ring-1 ring-sky-200             @break
                        @case('sprint')      bg-amber-50 text-amber-700 ring-1 ring-amber-200       @break
                        @case('quantitativa')bg-violet-50 text-violet-700 ring-1 ring-violet-200     @break
                    @endswitch">
                    {{ [
                        'domestica'=>'Linea Domestica',
                        'ufficio'=>'Linea Ufficio',
                        'sprint'=>'Linea Sprint',
                        'quantitativa'=>'Linea Quantitativa'
                    ][$line] }}
                </span>
            </div>
        </div>
    </div>

    {{-- Tabella --}}
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 shadow-sm rounded-2xl overflow-hidden">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Ordine</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Cliente</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Codice</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wide">Q.tà</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wide">Totale</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Data</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Settimana</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Note</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse ($orders as $order)
                    @foreach ($order->lines as $line)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium">{{ $order->num_ordine }}</td>
                            <td class="px-4 py-3 text-sm font-semibold">{{ $order->client->ragione_sociale ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $line->article_id }}</td>
                            <td class="px-4 py-3 text-sm text-right tabular-nums">{{ $line->quantita }}</td>
                            <td class="px-4 py-3 text-sm text-right tabular-nums font-semibold">{{ $line->total ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm">{{ \Illuminate\Support\Carbon::parse($order->data_ordine)->translatedFormat('d M Y') }}</td>
                            <td class="px-4 py-3 text-sm">#{{ \Illuminate\Support\Carbon::parse($order->data_ordine)->weekOfYear }}</td>
                            <td class="px-4 py-3 text-sm">
                                <textarea
                                    class="w-full border border-gray-200 rounded-lg px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-300"
                                    placeholder="Aggiungi nota…"
                                    x-data
                                    x-init="this.value = localStorage.getItem('note_{{ $line->id }}') ?? ''"
                                    @input="localStorage.setItem('note_{{ $line->id }}', $event.target.value)"
                                ></textarea>
                                <div class="mt-1 text-[11px] text-gray-400">Auto-salvataggio locale</div>
                            </td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-6 text-center text-sm text-gray-500">
                            Nessun ordine per i filtri selezionati.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
