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
    public $preassembleds = [];

    public function processData($lottos) : array {
        if ($lottos) {
            foreach($lottos as $l){
                array_push($this->preassembleds, PreAssembled::where('id', $l->pre_assembled_id)->get());
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
            })
            ->get();
            $preassembleds = $this->processData($lottos);
        }else if ($this->query) {
            $lottos = Lotto::query()
            ->when($this->query, function ($query) {
                $query->whereHas('preassembled', function ($subQuery) {
                    $subQuery->where('description', 'like', '%' . $this->query . '%');
                });
            })
            ->get();
            $preassembleds = $this->processData($lottos);
        }else if ($this->queryDate) {
            $lottos = Lotto::query()
            ->when($this->query, function ($query) {
                $query->where('created_at', 'like', '%' . $this->query . '%');
            })
            ->get();
            $preassembleds = $this->processData($lottos);
        } else {
            $lottos = Lotto::all();
            $preassembleds = $this->processData($lottos);
        }
        return view('livewire.lottos-table', compact('lottos', 'preassembleds'));
    }
}
