<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Lotto;
class SupplierCodeToLottosTable extends Component
{
    public string $supplier_code = '';
    public $lottos = [];

    public function mount(){
        $this->lottos = collect();
    }

    public function search2()
    {
         $this->lottos = Lotto::whereHas('lottoArticles', function ($query) {
            $query->where('supplier_code', $this->supplier_code);
        })->get();

    }
    public function render()
    {
        return view('livewire.supplier-code-to-lottos-table');
    }
}
