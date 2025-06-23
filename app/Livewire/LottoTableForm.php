<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Lotto;
use App\Models\LottoArticle;
use App\Models\Preassembled;
use App\Models\Article;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class LottoTableForm extends Component
{
    public $code_lotto;
    public $pre_assembled_id;
    public $quantity;
    public $components = [];
    public $lottoId;
    public $preAssembled;
    public $lottoCode;
    public $supplierCodes = [];
    public $articles = [];

    public function mount($lottoId = null, $articles = [], $preAssembled = null, $lottoCode = null, $supplierCodes = [], $components = [])
    {
        $this->articles = $articles;
        $this->preAssembled = $preAssembled;
        $this->lottoCode = $lottoCode;
        $this->supplierCodes = $supplierCodes;
        $this->components = $components;

        if ($lottoId) {
            $this->lottoId = $lottoId;
            $lotto = \App\Models\Lotto::findOrFail($lottoId);
            $this->code_lotto = $lotto->code_lotto;
            $this->pre_assembled_id = $lotto->pre_assembled_id;
            $this->quantity = $lotto->quantity;

            $articles = \App\Models\LottoArticle::where('lotto_id', $lottoId)->get();
            foreach ($articles as $article) {
                $this->components[] = [
                    'article_id' => $article->article_id,
                    'supplier_code' => $article->supplier_code,
                ];
            }
        }
    }

    protected function rules()
    {
        $rules = [
            'code_lotto' => 'required|string',
            'pre_assembled_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
        ];

        foreach ($this->components as $index => $component) {
            $article = Article::find($component['article_id']);
            if ($article && $article->is_moca) {
                $rules["components.$index.supplier_code"] = 'required|string';
            }
        }

        return $rules;
    }

    public function submit()
    {
        $this->validate();
        logger('Submit triggered');
        $oldSupplierCode = [];
        $newSupplierCode = [];

        if ($this->lottoId) {
            $lotto = Lotto::findOrFail($this->lottoId);
            $lotto->update([
                'quantity' => $this->quantity,
            ]);
        } else {
            $lotto = Lotto::create([
                'code_lotto' => $this->code_lotto,
                'pre_assembled_id' => $this->pre_assembled_id,
                'quantity' => $this->quantity,
            ]);
            $this->lottoId = $lotto->id;
        }

        foreach ($this->components as $component) {
            $pivot = LottoArticle::where('lotto_id', $lotto->id)
                ->where('article_id', $component['article_id'])
                ->first();

            $oldSupplierCode[] = $pivot->supplier_code ?? null;

            LottoArticle::updateOrCreate(
                [
                    'lotto_id' => $lotto->id,
                    'article_id' => $component['article_id']
                ],
                [
                    'supplier_code' => $component['supplier_code'] ?? null,
                ]
            );

            $newSupplierCode[] = $component['supplier_code'];
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $this->lottoId ? 'updated' : 'created',
            'table_name' => 'lotto_articles',
            'record_id' => $lotto->id,
            'changed_data' => [$oldSupplierCode, $newSupplierCode],
        ]);

        $pdf = Pdf::loadView('pdf.lotto', [
            'lotto' => $lotto,
            'components' => $this->components,
            'preAssembled' => Preassembled::find($this->pre_assembled_id),
            'lottoCode' => $this->code_lotto,
            'quantity' => $this->quantity,
            'date' => now()->format('d/m/Y'),
            'articles' => Article::whereIn('id', collect($this->components)->pluck('article_id'))->get(),
            'supplier_codes' => collect($this->components)->pluck('supplier_code')->toArray(),
        ]);

        $folderPath = storage_path('app/public/lottos');
        if (!file_exists($folderPath)) mkdir($folderPath, 0777, true);

        $pdf->save($folderPath . '/' . $this->code_lotto . '.pdf');

        $redirectRoute = Auth::user()->role === 'Admin' ? 'admin.index' : 'operator.index';
        return redirect()->route($redirectRoute)->with('success', 'Lotto gestito con successo.');
    }

    public function render()
    {
        return view('livewire.lotto-table-form');
    }
}
