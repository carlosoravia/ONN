<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Lotto;
use App\Models\PreAssembled;

class LottosTable extends Component
{
    public $query = '';
    public $queryCode = '';
    public $queryDate = '';

    public function render()
    {
        $lottos = Lotto::with('preassembled')
            ->when($this->queryCode, function ($query) {
                $query->where('code_lotto', 'like', '%' . $this->queryCode . '%');
            })
            ->when($this->query, function ($query) {
                $query->whereHas('preassembled', function ($subQuery) {
                    $subQuery->where('description', 'like', '%' . $this->query . '%');
                });
            })
            ->when($this->queryDate, function ($query) {
                $query->whereRaw('DAY(created_at) = ?', [$this->queryDate]);
            })
            ->orderBy('id', 'desc')
            ->get();

        return view('livewire.lottos-table', compact('lottos'));
    }
}
