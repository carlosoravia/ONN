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


class OperatorController extends Controller
{
    public function index(){
        $lottos = Lotto::where('created_at', '>=', date_format(now(), 'Y-m-d'))->orderBy('id', 'DESC')->get();
        $lastLotto = Lotto::orderBy('created_at', 'desc')->first();
        $preassembleds = [];
        foreach ($lottos as $lotto) {
            array_push($preassembleds, PreAssembled::where('id', $lotto->pre_assembled_id)->first());
        }
        $lottosCount = $lottos->count();
        return view('operator.index', compact('lottos', 'lastLotto', 'lottosCount', 'preassembleds'));
    }

    public function selectPreAssembled(){
        return view('operator.select-pre-assembled');
    }

    public function lottoCreate($lottoId, LottoService $lottoService)
    {
        $articles = [];
        $supplierCodes = [];
        $lottoCode = $lottoService->generaCodiceLotto();
        $components = PreassembledArticle::where('pre_assembled_id', $lottoId)->get();
        $preAssembled = PreAssembled::where('id', $lottoId)->first();
        foreach ($components as $component) {
            array_push($articles, Article::where('id', $component->article_id)->first());
            $supplierCodes = LottoArticle::where('article_id', $component->article_id)
                                 ->pluck('supplier_code')->toArray();
        }
        return view('operator.create-lotto', compact('articles', 'preAssembled', 'lottoCode', 'supplierCodes', 'components'))
            ->with('lottoCode', $lottoCode);
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
            array_push($preassembleds, PreAssembled::where('id', $lotto->pre_assembled_id)->first());
        }
        return view('operator.lottos-show', compact('lottos', 'preassembleds'));
    }

    public function editLotto($lottoId)
    {
        $articles = [];
        $supplierCodes = [];
        $lotto = Lotto::findOrFail($lottoId);
        $lottoCode = $lotto->code_lotto;
        $preAssembled = PreAssembled::findOrFail($lotto->pre_assembled_id);
        $components = LottoArticle::where('lotto_id', $lottoId)->get();
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
