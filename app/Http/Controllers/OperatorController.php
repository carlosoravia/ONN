<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Lotto;
use App\Models\PreAssembled;
use App\Models\LottoArticle;
use App\Models\PreassembledArticle;
use App\Models\Article;
use App\Models\AuditLog;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\GenerateLottoNumberService as LottoService;
use App\Services\AuditLogService;
use App\Services\PivotAuditService;


class OperatorController extends Controller
{
    public function index(Request $request){
        $lottos = Lotto::where('created_at', '>=', date_format(now(), 'Y-m-d'))->orderBy('id', 'DESC')->get();
        $lastLotto = Lotto::orderBy('created_at', 'desc')->first();
        $preassembleds = [];
        foreach ($lottos as $lotto) {
            array_push($preassembleds, PreAssembled::where('id', $lotto->pre_assembled_id)->first());
        }
        $lottosCount = $lottos->count();
        return view('operator.index', compact('lottos', 'lastLotto', 'lottosCount', 'preassembleds'));
    }

    public $lottoCode;
    public $articles = [];
    public $supplierCodes = [];
    public array $values = [];

    public function submitLotto(Request $request)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'code_lotto' => 'required|string|max:255',
        ], [
            'quantity.required' => 'Il numero di pezzi è obbligatorio.',
            'quantity.min' => 'Il numero deve contenere almeno :min caratteri.',
            'code_lotto.required' => 'Il codice lotto è obbligatorio.',
            'code_lotto.max' => 'Il codice lotto non può superare i :max caratteri.',
            'code_lotto.string' => 'Il codice lotto deve essere una stringa valida.',
        ]);
        $lotto = Lotto::create(
            ['code_lotto' => $request->code_lotto,
            'pre_assembled_id' => $request->pre_assembled_id,
            'quantity' => $request->quantity]
        );
        AuditLogService::log('created', 'creato lotto', $lotto);

        foreach ($request->components as $component) {
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
            'components' => $request->components,
            'preAssembled' => Preassembled::find($request->pre_assembled_id),
            'lottoCode' => $request->code_lotto,
            'quantity' => $request->quantity,
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

        if(Auth::user()->role === "Admin"){
            return redirect()->route('admin.index')
            ->with('success', 'Lotto creato con successo');
        } else if (Auth::user()->role === "Operator") {
            return redirect()->route('operator.index')
            ->with('success', 'Lotto creato con successo');
        }
    }

    public function selectPreAssembled(){
        $preassembleds = Preassembled::all();
        return view('operator.select-pre-assembled', compact('preassembleds'));
    }

    public function lottoCreate($id, LottoService $lottoService)
    {
        $articles = [];
        $supplierCodes = [];
        $lottoCode = $lottoService->generaCodiceLotto();
        $components = PreassembledArticle::where('pre_assembled_id', $id)->get();
        $preAssembled = Preassembled::where('id', $id)->first();
        foreach ($components as $component) {
            array_push($articles, Article::where('id', $component->article_id)->first());
            $supplierCodes = LottoArticle::where('article_id', $component->article_id)
                                 ->pluck('supplier_code')->toArray();
        }
        return view('operator.create-lotto', compact('articles', 'preAssembled', 'lottoCode', 'supplierCodes'));
    }

    public function selectLotto(){
        $preassembleds = [];
        $data = [];
        $lottos = Lotto::all();
        if ($lottos->isEmpty()) {
            if(Auth::user()->role === "Admin"){
                return redirect()->route('admin.index')
                ->with('error', 'Nessun lotto disponibile per la modifica.');
            } else if (Auth::user()->role === "Operator") {
                return redirect()->route('operator.index')
                ->with('error', 'Nessun lotto disponibile per la modifica.');
            }
        }
        foreach ($lottos as $lotto) {
            array_push($preassembleds, Preassembled::where('id', $lotto->pre_assembled_id)->first());
        }
        return view('operator.lottos-show', compact('lottos', 'preassembleds'));
    }

    public function editLotto($id)
    {
        $articles = [];
        $supplierCodes = [];
        $lotto = Lotto::findOrFail($id);
        $lottoCode = $lotto->code_lotto;
        $preAssembled = Preassembled::findOrFail($lotto->pre_assembled_id);
        $components = LottoArticle::where('lotto_id', $id)->get();
        foreach ($components as $component) {
            array_push($articles, Article::where('id', $component->article_id)->first());
            array_push($supplierCodes, LottoArticle::where('article_id', $component->article_id)->first());
        }
        return view('operator.lotto-edit', compact('lotto', 'preAssembled', 'components', 'lottoCode', 'articles', 'supplierCodes'));
    }

    public function updateLotto(Request $request){
        $lotto = Lotto::where('code_lotto', $request->code_lotto)->first();
        $oldSupplierCode = [];
        $newSupplierCode = [];
        $lotto->update(
        [
            'quantity' => $request->quantity
        ]
        );
        foreach ($request->components as $component) {

            $pivot = LottoArticle::where('lotto_id', $lotto->id)
                ->where('article_id', $component['article_id'])
                ->first();

            array_push($oldSupplierCode, $pivot->supplier_code ?? null);
            LottoArticle::updateOrCreate(
                [
                    'lotto_id' => $lotto->id,
                    'article_id' => $component['article_id']
                ],
                [
                    'supplier_code' => $component['supplier_code'] ?? null,
                ]
            );
            array_push($newSupplierCode, $component['supplier_code']);
        }
        AuditLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'updated',
            'table_name'  => 'lotto_articles',
            'record_id'   => $lotto->id,
            'changed_data'=> [$oldSupplierCode, $newSupplierCode],
        ]);
        if(Auth::user()->role === "Admin"){
            return redirect()->route('admin.index')
            ->with('success', 'Lotto aggiornato con successo');
        } else if (Auth::user()->role === "Operator") {
            return redirect()->route('operator.index')
            ->with('success', 'Lotto aggiornato con successo');
        }
    }
}
