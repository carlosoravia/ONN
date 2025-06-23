<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PreAssembled;

class PreassembledTable extends Component
{
    public $query = '';
    public $queryCode = '';

    public function render()
    {
        if ($this->queryCode) {
            $this->queryCode = trim($this->queryCode);
            $preassembleds = PreAssembled::query()
            ->when($this->queryCode, function ($queryCode) {
                $queryCode->where('code', 'like', '%' . $this->queryCode . '%');
            })
            ->get();
        }else if ($this->query) {
            $preassembleds = PreAssembled::query()
            ->when($this->query, function ($query) {
                $query->where('description', 'like', '%' . $this->query . '%');
            })
            ->get();
        } else {
            $preassembleds = PreAssembled::all();
        }
        return view('livewire.preassembled-table', compact('preassembleds'));
    }
}
