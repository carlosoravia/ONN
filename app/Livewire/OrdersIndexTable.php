<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\Order;

class OrdersIndexTable extends Component
{
     /** UI state */
    public string $currentMonth;         // "YYYY-MM"
    public string $line = 'domestica';   // domestica|ufficio|sprint|quantitativa
    public ?int $week = null;            // settimana custom selezionata (o null = tutte)

    /** Config settimane custom */
    public string $weekStartDate = '2025-07-01'; // data inizio stagione (lunedì sarà normalizzato)
    public int $weekStartNumber = 33;            // numero di partenza (es. 33)

    /** Navigazione mesi */
    public array $months = [];            // [{key:'YYYY-MM', label:'Ottobre 2025'}, ...]
    public array $weeksForMonth = [];     // es. [33,34,35,36]

    /** Mock data */
    public array $rows = [];

    public function mount(): void
    {
        // mese corrente
        $this->currentMonth = now()->format('Y-m');

        // intervallo 3 indietro + 12 avanti
        $start = now()->startOfMonth()->subMonths(3);
        $end   = now()->startOfMonth()->addMonths(12);
        for ($dt = $start->copy(); $dt <= $end; $dt->addMonth()) {
            $this->months[] = [
                'key'   => $dt->format('Y-m'),
                'label' => Str::ucfirst($dt->translatedFormat('F Y')),
            ];
        }
        // calcola settimane del mese corrente
        $this->weeksForMonth = $this->computeWeeksForMonth($this->currentMonth);
    }

    /** Navigazione mesi */
    public function prevMonth(): void
    {
        $i = collect($this->months)->search(fn ($m) => $m['key'] === $this->currentMonth);
        if ($i !== false && $i > 0) {
            $this->currentMonth = $this->months[$i - 1]['key'];
            $this->week = null;
            $this->weeksForMonth = $this->computeWeeksForMonth($this->currentMonth);
        }
    }

    public function nextMonth(): void
    {
        $i = collect($this->months)->search(fn ($m) => $m['key'] === $this->currentMonth);
        if ($i !== false && $i < count($this->months) - 1) {
            $this->currentMonth = $this->months[$i + 1]['key'];
            $this->week = null;
            $this->weeksForMonth = $this->computeWeeksForMonth($this->currentMonth);
        }
    }

    public function goCurrent(): void
    {
        $this->currentMonth = now()->format('Y-m');
        $this->week = null;
        $this->weeksForMonth = $this->computeWeeksForMonth($this->currentMonth);
    }

    public function setLine(string $line): void
    {
        $this->line = $line;
    }

    public function selectWeek(?int $w): void
    {
        $this->week = $w;
    }

    /** ---------- Computed ---------- */

    public function getFilteredRowsProperty()
    {
        $month = $this->currentMonth;

        return collect($this->rows)
            ->filter(fn ($r) => Carbon::parse($r['date'])->format('Y-m') === $month)
            ->filter(fn ($r) => $r['line'] === $this->line)
            ->when($this->week !== null, function ($c) {
                return $c->filter(function ($r) {
                    return $r['week'] === $this->week;
                });
            })
            ->values()
            ->all();
    }

    public function getKpisProperty(): array
    {
        $rows = $this->filteredRows;

        $cnt   = count($rows);
        $qty   = array_sum(array_column($rows, 'quantity'));
        $total = array_sum(array_column($rows, 'total'));

        return compact('cnt', 'qty', 'total');
    }

    /** ---------- Helpers ---------- */

    /** numerazione settimana custom continua */
    protected function customWeekNumber(Carbon $date): int
    {
        $base = Carbon::parse($this->weekStartDate)->startOfWeek(Carbon::MONDAY);
        $d    = $date->copy()->startOfWeek(Carbon::MONDAY);
        $delta = $base->diffInWeeks($d, false);
        return $this->weekStartNumber + $delta;
    }

    /** set di settimane (ordinate, uniche) per un mese "YYYY-MM" */
    protected function computeWeeksForMonth(string $ym): array
    {
        [$y, $m] = explode('-', $ym);
        $first = Carbon::createFromDate((int)$y, (int)$m, 1)->startOfMonth();
        $last  = $first->copy()->endOfMonth();

        $cursor = $first->copy()->startOfWeek(Carbon::MONDAY);
        $end    = $last->copy()->endOfWeek(Carbon::SUNDAY);

        $weeks = [];
        while ($cursor <= $end) {
            $weeks[] = $this->customWeekNumber($cursor);
            $cursor->addWeek();
        }
        return array_values(array_unique($weeks));
    }

    public function render()
    {
        // Filtra ordini per mese selezionato
        $month = $this->currentMonth;
        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        $orders = Order::whereBetween('data_ordine', [$startDate, $endDate])
            ->with(['client', 'lines'])
            ->orderBy('data_ordine', 'desc')
            ->get();

        return view('livewire.orders-index-table', compact('orders'));
    }
}
