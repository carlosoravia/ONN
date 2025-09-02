<?php

namespace App\Livewire;

use App\Models\Article;
use App\Models\AuditLog;
use App\Models\Lotto;
use App\Models\LottoArticle;
use App\Models\Preassembled;
use App\Models\PreassembledArticle;
use App\Services\AuditLogService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Servixes\LottoService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Services\GenerateLottoNumberService as LottoCodeGenerator;
use Livewire\Component;

class LottoSave extends Component
{
    public $lottoId;
    public $lottoCode;
    public $code_lotto;
    public $preassembleds;
    public $pre_assembled_id;
    public $quantity;
    public $supplierCodes = [];
    public $components = [];
    public $articles = [];
    public $lotto;
    public bool $showValidationModal = false;
    public string $context;

   public function mount($preassembleds, $lottoCode, $articles, $components, ?Lotto $lotto = null, $supplierCodes, $context = 'default')
    {
        $this->preassembleds = $preassembleds;
        $this->pre_assembled_id = $preassembleds->id;
        $this->code_lotto = $lottoCode;
        $this->articles = $articles;
        $this->lotto = $lotto;
        $this->quantity = $lotto->quantity ?? null;
        $this->context = $context;

        if ($lotto && isset($lotto->id)) {
            $this->lottoId = $lotto->id;
            $this->components = LottoArticle::where('lotto_id', $lotto->id)
                ->get()
                ->map(function ($component) {
                    return [
                        'article_id' => $component->article_id,
                        'supplier_code' => $component->supplier_code,
                    ];
                })->toArray();
        } else {
            $this->components = collect($components)->map(function ($component) use ($supplierCodes) {
                $articleId = $component->article_id;
                $supplierCode = $supplierCodes[$articleId]['supplier_code'] ?? null;
                return [
                    'article_id' => $articleId,
                    'supplier_code' => $supplierCode,
                ];
            })->toArray();
        }
    }
    public function getErrorCountProperty()
    {
        return count($this->getErrorBag()->all());
    }
    public function messages()
    {
        $messages = [
            'code_lotto.required' => 'Il codice lotto è obbligatorio.',
            'quantity.required' => 'La quantità è obbligatoria.',
            'quantity.numeric' => 'La quantità deve essere un numero.',
            'quantity.min' => 'La quantità deve essere almeno 1.',
            'pre_assembled_id.required' => 'Il pre-assemblato è obbligatorio.',
            'pre_assembled_id.exists' => 'Il pre-assemblato selezionato non è valido.',
        ];
        foreach ($this->components as $index => $component) {
            $articleId = $component['article_id'] ?? null;
            $article = collect($this->articles)->firstWhere('id', $articleId);

            if ($article && $article->is_moca) {
                $messages["components.$index.supplier_code.required"] = "Il codice fornitore è obbligatorio per l'articolo \"{$article->description}\".";
            }
        }

        return $messages;
    }

    public function rules()
    {
        $rules = [
            'code_lotto' => 'required|string',
            'quantity' => 'required|numeric|min:1',
            'pre_assembled_id' => 'required',
        ];

        foreach ($this->components as $index => $component) {
            $articleId = $component['article_id'] ?? null;
            $article = collect($this->articles)->firstWhere('id', $articleId);

            if ($article && $article->is_moca) {
                $rules["components.$index.supplier_code"] = 'required|string';
            } else {
                $rules["components.$index.supplier_code"] = 'nullable|string';
            }
        }

        return $rules;
    }


    public function submit()
    {
        try {
            $this->resetErrorBag();
            $this->resetValidation();
            $this->validate($this->rules());
            $this->articles = [];
            $this->supplierCodes = [];
            $lotto = Lotto::where('code_lotto', $this->code_lotto)->first();
            if ($this->context === 'create') {
                $lotto = Lotto::create([
                    'code_lotto' => $this->code_lotto,
                    'pre_assembled_id' => $this->pre_assembled_id,
                    'quantity' => $this->quantity
                ]);
                foreach ($this->components as $component) {
                    LottoArticle::create([
                        'lotto_id' => $lotto->id,
                        'article_id' => $component['article_id'],
                        'supplier_code' => $component['supplier_code'] ?? null,
                    ]);
                    array_push($this->articles, Article::where('id', $component['article_id'])->first());
                    array_push($this->supplierCodes, $component['supplier_code']);
                }
                $pdf = Pdf::loadView('pdf.lotto', [
                    'lotto' => $lotto,
                    'components' => $this->components,
                    'preassembleds' => Preassembled::find($this->pre_assembled_id),
                    'lottoCode' => $this->code_lotto,
                    'quantity' => $this->quantity,
                    'date' => now()->format('d/m/Y'),
                    'articles' => $this->articles,
                    'supplier_codes' =>  $this->supplierCodes,
                ]);
                $folderPath = storage_path('app/public/lottos');
                if (!file_exists($folderPath)) {
                    \Log::info("Cartella non trovata, la creo: $folderPath");
                    mkdir($folderPath, 0777, true);
                }
                $filename = $lotto->code_lotto . '.pdf';
                $fullPath = $folderPath . DIRECTORY_SEPARATOR . $filename;
                $pdf->save($fullPath);
                AuditLogService::log('created', 'creato lotto', $lotto);
            }else if($this->context === 'edit'){
                $lotto = Lotto::findOrFail($this->lottoId);
                $oldSupplierCode = [];
                $newSupplierCode = [];
                $lotto->update([
                    'quantity' => $this->quantity
                ]);
                foreach ($this->components as $component) {
                    $pivot = LottoArticle::where('lotto_id', $lotto->id)
                        ->where('article_id', $component['article_id'])
                        ->first();

                    array_push($oldSupplierCode, $pivot->supplier_code ?? null);
                    array_push($newSupplierCode, $component['supplier_code'] ?? null);
                    LottoArticle::updateOrCreate(
                        [
                            'lotto_id' => $lotto->id,
                            'article_id' => $component['article_id']
                        ],
                        [
                            'supplier_code' => $component['supplier_code'] ?? null,
                        ]
                    );
                    array_push($this->articles, Article::where('id', $component['article_id'])->first());
                    array_push($this->supplierCodes, $component['supplier_code']);
                }
                //dd([$oldSupplierCode, $newSupplierCode]);
                AuditLog::create([
                    'user_id'     => Auth::id(),
                    'action'      => 'updated',
                    'table_name'  => 'lotto_articles',
                    'record_id'   => $lotto->id,
                    'changed_data'=> [$oldSupplierCode, $newSupplierCode],
                ]);
                $folderPath = storage_path('app/public/lottos');
                $filename = $lotto->code_lotto . '.pdf';
                $fullPath = $folderPath . DIRECTORY_SEPARATOR . $filename;
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
                // --- RIGENERA IL PDF ---
                $pdf = Pdf::loadView('pdf.lotto', [
                    'lotto' => $lotto,
                    'components' => $this->components,
                    'preassembleds' => Preassembled::find($this->pre_assembled_id),
                    'lottoCode' => $this->code_lotto,
                    'quantity' => $this->quantity,
                    'date' => now()->format('d/m/Y'),
                    'articles' => $this->articles,
                    'supplier_codes' =>  $this->supplierCodes,
                ]);
                $pdf->save($fullPath);
            }else if($this->context === 'create-from-existing'){
                $newLotto = Lotto::create([
                    'code_lotto' => $this->code_lotto,
                    'pre_assembled_id' => $this->pre_assembled_id,
                    'quantity' => $this->quantity
                ]);
                $lotto = Lotto::findOrFail($this->lottoId);
                $oldSupplierCode = [];
                $newSupplierCode = [];
                foreach ($this->components as $component) {
                    $pivot = LottoArticle::where('lotto_id', $lotto->id)
                        ->where('article_id', $component['article_id'])
                        ->first();

                    array_push($oldSupplierCode, $pivot->supplier_code ?? null);
                    array_push($newSupplierCode, $component['supplier_code'] ?? null);
                    LottoArticle::Create(
                        [
                            'lotto_id' => $newLotto->id,
                            'article_id' => $component['article_id'],
                            'supplier_code' => $component['supplier_code'] ?? null,
                        ]
                    );
                    array_push($this->articles, Article::where('id', $component['article_id'])->first());
                    array_push($this->supplierCodes, $component['supplier_code']);
                }
                AuditLog::create([
                    'user_id'     => Auth::id(),
                    'action'      => 'created',
                    'table_name'  => 'lotto_articles',
                    'record_id'   => $lotto->id,
                    'changed_data'=> [$oldSupplierCode, $newSupplierCode],
                ]);
                $folderPath = storage_path('app/public/lottos');
                $filename = $lotto->code_lotto . '.pdf';
                $fullPath = $folderPath . DIRECTORY_SEPARATOR . $filename;
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
                // --- RIGENERA IL PDF ---
                $pdf = Pdf::loadView('pdf.lotto', [
                    'lotto' => $lotto,
                    'components' => $this->components,
                    'preassembleds' => Preassembled::find($this->pre_assembled_id),
                    'lottoCode' => $this->code_lotto,
                    'quantity' => $this->quantity,
                    'date' => now()->format('d/m/Y'),
                    'articles' => $this->articles,
                    'supplier_codes' =>  $this->supplierCodes,
                ]);
                $pdf->save($fullPath);
            }
            return redirect()->route(
                Auth::user()->role === 'Admin' ? 'admin.index' : 'operator.index'
            )->with('success', 'Lotto salvato con successo');
        } catch (ValidationException $e) {
            $this->dispatchBrowserEvent('showModal');
            throw $e;
        }
    }
    public function render()
    {
        return view('livewire.lotto-save');
    }
}
