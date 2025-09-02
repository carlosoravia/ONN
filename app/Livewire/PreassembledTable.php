<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Preassembled;

class PreassembledTable extends Component
{
    public $query = '';
    public $queryCode = '';
    public string $context = 'default';

    public function mount($context = 'default')
    {
        $this->context = $context;
    }
    public function render()
    {
        if ($this->queryCode) {
            $preassembleds = Preassembled::query()
            ->when($this->queryCode, function ($queryCode) {
                $queryCode->where('code', 'like', '%' . $this->queryCode . '%');
            })
            ->get();
        }else if ($this->query) {
            $preassembleds = Preassembled::query()
            ->when($this->query, function ($query) {
                $query->where('description', 'like', '%' . $this->query . '%');
            })
            ->get();
        } else {
            $preassembleds = Preassembled::with('latestLotto')->get();
        }
        return view('livewire.preassembled-table', compact('preassembleds'));
    }
}
