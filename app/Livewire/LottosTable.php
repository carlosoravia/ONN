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
        if ($this->queryCode) {
            $lottos = Lotto::query()
            ->when($this->queryCode, function ($queryCode) {
                $queryCode->where('code_lotto', 'like', '%' . $this->queryCode . '%');
            })->orderBy('id', 'desc')
            ->get();
        }else if ($this->query) {
            $lottos = Lotto::query()
            ->when($this->query, function ($query) {
                $query->whereHas('preassembled', function ($subQuery) {
                    $subQuery->where('description', 'like', '%' . $this->query . '%');
                });
            })->orderBy('id', 'desc')
            ->get();
        }else if ($this->queryDate) {
            $lottos = Lotto::whereRaw('DAY(created_at) = ?', [$this->queryDate])
                ->orderBy('id', 'desc')
                ->get();
        } else {
            $lottos = Lotto::orderBy('id', 'desc')->get();
        }
        return view('livewire.lottos-table', compact('lottos'));
    }
}
