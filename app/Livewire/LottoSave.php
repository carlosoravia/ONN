<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Lotto;
use App\Models\Article;
use App\Models\LottoArticle;
use App\Models\Preassembled;
use App\Services\LottoService;
use Illuminate\Support\Facades\Log;

class LottoSave extends Component
{

public $lottoCode;
    public $preAssembled;
    public $pre_assembled_id;
    public $code_lotto;
    public $quantity;
    public $supplierCodes;
    public $components = [];
    public $articles = [];
    public $lotto = null;

    public function mount($preAssembled, $lottoCode, $articles, $components, $lotto = null)
    {
        $this->preAssembled;
        $this->pre_assembled_id;
        $this->code_lotto = $lottoCode;
        $this->articles = $articles;
        $this->lotto = $lotto;
        $this->quantity = $lotto->quantity ?? null;
        $this->components = $components;
    }

    public function rules()
    {
        $rules = [
            'code_lotto' => 'required|string',
            'quantity' => 'required|numeric|min:1',
        ];

        foreach ($this->components as $index => $component) {
            $article = $this->articles[$index];
            if ($article->is_moca) {
                $rules["components.$index.supplier_code"] = 'required|string';
            }
        }

        return $rules;
    }

    public function submit()
    {
        $this->validate();

        $lotto = Lotto::updateOrCreate(
            ['code_lotto' => $this->code_lotto],
            [
                'pre_assembled_id' => $this->pre_assembled_id,
                'quantity' => $this->quantity,
            ]
        );

        foreach ($this->components as $component) {
            LottoArticle::updateOrCreate(
                [
                    'lotto_id' => $lotto->id,
                    'article_id' => $component['article_id'],
                ],
                [
                    'supplier_code' => $component['supplier_code'] ?? null,
                ]
            );
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'created',
            'table_name' => 'lotto_articles',
            'record_id' => $lotto->id,
            'changed_data' => $this->components,
        ]);

        return redirect()->route(
            Auth::user()->role === 'Admin' ? 'admin.index' : 'operator.index'
        )->with('success', 'Lotto salvato con successo');
    }
    public function render()
    {
        return view('livewire.lotto-save');
    }
}
