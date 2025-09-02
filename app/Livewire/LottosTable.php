<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Lotto;
use App\Models\Preassembled;

class LottosTable extends Component
{
    public $query = '';
    public $queryCode = '';
    public $queryDate = '';
    public $preassembleds = [];

    public function processData($lottos) : array {
        if ($lottos) {
            foreach($lottos as $l){
                array_push($this->preassembleds, Preassembled::where('id', $l->pre_assembled_id)->get());
            }
            return $this->preassembleds;
        }else{
            return [];
        }
    }

    public function render()
    {
        if ($this->queryCode) {
            $lottos = Lotto::query()
            ->when($this->queryCode, function ($queryCode) {
                $queryCode->where('code_lotto', 'like', '%' . $this->queryCode . '%');
            })->orderBy('id', 'desc')
            ->get();
            $preassembleds = $this->processData($lottos);
        }else if ($this->query) {
            $lottos = Lotto::query()
            ->when($this->query, function ($query) {
                $query->whereHas('preassembleds', function ($subQuery) {
                    $subQuery->where('description', 'like', '%' . $this->query . '%');
                });
            })->orderBy('id', 'desc')
            ->get();
            $preassembleds = $this->processData($lottos);
        }else if ($this->queryDate) {
            $lottos = Lotto::whereRaw('DAY(created_at) = ?', [$this->queryDate])
                ->orderBy('id', 'desc')
                ->get();
            $preassembleds = $this->processData($lottos);
        } else {
            $lottos = Lotto::orderBy('id', 'desc')->get();
            $preassembleds = $this->processData($lottos);
        }
        return view('livewire.lottos-table', compact('lottos', 'preassembleds'));
    }
}
