<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AuditLog;
use App\Models\Lotto;
use App\Models\Article;
use Illuminate\Support\Facades\DB;
class AdminController extends Controller
{
    public function index(){
        $audits = AuditLog::orderBy('created_at', 'desc')->take(20)->get();
        $lottos = Lotto::where('created_at', '>=', date_format(now(), 'Y-m-d'))->get();
        $lastLotto = Lotto::orderBy('created_at', 'desc')->first();
        $lottosCount = $lottos->count();
        // $articoli = DB::connection('mago')->table('CI_BOM')->get();
        // dd($articoli);
        return view('admin.index', compact('audits', 'lastLotto', 'lottosCount'));
    }

    public function editUsers(){
        return view('admin.edit-users');
    }

    public function showArticles(){
        return view('admin.edit-articles');
    }

    public function showAuditLogs($id)
    {
        $lotto = '';
        $audit = AuditLog::findOrFail($id);
        $record = $audit->getTargetRecord();
        $user = $audit->user;
        $linkToRecord = $audit->getRecordUrl();
        if ($audit->table_name === 'lotto_articles' && $audit->action === 'updated') {
            $lotto = Lotto::find($record->lotto_id);
            $lottoCode = $lotto->code_lotto;
        }else if ($audit->table_name === 'lottos') {
            $lotto = Lotto::find($record->id);
            $lottoCode = $lotto->code_lotto;
        } else {
            $lottoCode = '';
        }
        $updatedData = is_string($audit->changed_data)
            ? json_decode($audit->changed_data, true)
            : $audit->changed_data;
        return view('admin.show-audit-logs', compact('audit','record', 'user', 'updatedData', 'lottoCode', 'linkToRecord'));
    }



    public function deleteUser($id)
    {
        $user = User::findOrFail($id)->delete();
        AuditLogService::log('deleted', 'elimato utente', $user);
        return redirect()->back()->with('success', 'Utente eliminato con successo.');
    }

    public function makeAdmin($id)
    {
        $user = User::findOrFail($id);
        $user->role = 'admin';
        $user->save();
        AuditLogService::log('chanced role', 'cambiato permessi utente', $user);

        return redirect()->back()->with('success', 'Ruolo impostato su Admin.');
    }

    public function makeOperator($id)
    {
        $user = User::findOrFail($id);
        $user->role = 'operator';
        $user->save();
        AuditLogService::log('chanced role', 'cambiato permessi utente', $user);
        return redirect()->back()->with('success', 'Ruolo impostato su Operatore.');
    }

    public function createUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'operator_code' => 'required|integer|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,operator',
        ]);
        $fullName = strtoupper($request->name) . ' ' . strtoupper($request->surname);
        $user = User::create([
            'name' => $fullName,
            'operator_code' => $request->operator_code,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);
        AuditLogService::log('created', 'creato utente', $user);
        return redirect()->back()->with('success', 'Utente creato con successo.');
    }

    public function editArticles(Request $request, $id){
        $request->validate([
            'code' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'padre_description' => 'required|string|max:255',
            'is_moca' => 'required|boolean',
        ]);
        $article = Article::findOrFail($id);
        if (!$article) {
            return redirect()->back()->with('error', 'Articolo non trovato.');
        }
        $article->code = $request->code;
        $article->description = $request->description;
        $article->padre_description = $request->padre_description;
        $article->is_moca = $request->is_moca;
        $article->save();
        AuditLogService::log('edited', 'Cambiato articolo', $article);
        return redirect()->back()->with('success', 'Articolo aggiornato con successo.');
    }

    public function deleteArticle($id){
        $article = Article::findOrFail($id);
        $article->delete();
        AuditLogService::log('deleted', 'elimato articolo', $article);
        return redirect()->back()->with('success', 'Articolo eliminato con successo.');
    }
    public function updateArticle($id){
        $article = Article::findOrFail($id);
        $article->is_moca = !$article->is_moca;
        $article->save();
        AuditLogService::log('updated', 'modificato articolo', $article);
        return redirect()->back()->with('success', 'Articolo aggiornato con successo.');
    }
    public function createArticle(Request $request){

        if(!$request->has('is_moca')){
            $request->merge(['is_moca' => false]);
        }else {
            $request->merge(['is_moca' => true]);
        }
        $request->validate([
            'code' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'padre_description' => 'required|string|max:255',
            'is_moca' => 'boolean',
        ]);
        $article = Article::create([
            'code' => $request->code,
            'description' => $request->description,
            'padre_description' => $request->padre_description,
            'is_moca' => $request->is_moca,
        ]);
        AuditLogService::log('created', 'creato articolo', $article);
        return redirect()->back()->with('success', 'Articolo creato con successo.');
    }

}
