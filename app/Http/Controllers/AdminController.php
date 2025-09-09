<?php

namespace App\Http\Controllers;

use App\Models\Preassembled;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AuditLog;
use App\Models\Lotto;
use App\Models\Article;
use App\Models\PreassembledArticle;
use App\Services\AuditLogService;
class AdminController extends Controller
{
    /**
     * Mostra la dashboard admin con ultimi audit, lotto e conteggio lotti di oggi.
     *
     * @return \Illuminate\View\View
     */
    public function index(){
        $audits = AuditLog::orderBy('created_at', 'desc')->take(20)->get();
        $lottos = Lotto::where('created_at', '>=', date_format(now(), 'Y-m-d'))->get();
        $lastLotto = Lotto::orderBy('created_at', 'desc')->first();
        $lottosCount = $lottos->count();
        return view('admin.index', compact('audits', 'lastLotto', 'lottosCount'));
    }
    /**
     * Mostra la vista per modificare gli utenti.
     *
     * @return \Illuminate\View\View
     */
    public function editUsers(){
        return view('admin.edit-users');
    }
    /**
     * Mostra la vista per modificare gli articoli.
     *
     * @return \Illuminate\View\View
     */
    public function showArticles(){
        return view('admin.edit-articles');
    }
    /**
     * Mostra i dettagli di un audit log specifico.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
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
    /**
     * Elimina un utente e registra l'azione in AuditLog.
     *
     * @param int $id
     * @return void
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id)->delete();
        AuditLogService::log('deleted', 'elimato utente', $user);
    }
    /**
     * Imposta il ruolo di un utente in base alla scelta.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeRole($id, Request $request)
    {
        $user = User::findOrFail($id);
        switch ($request->role) {
            case 'Admin':
                $user->role = $request->role;
                break;
            case 'Operator':
                $user->role = $request->role;
                break;
            case 'Sales':
                $user->role = $request->role;
                break;
            default:
                return redirect()->back()->with('error', 'Ruolo non valido.');
        }
        $user->save();
        return redirect()->back()->with('success', 'Ruolo impostato su ' . $request->role . ' con successo.');
    }
    /**
     * Crea un nuovo utente con validazione.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
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
        return redirect()->back()->with('success', 'Utente creato con successo.');
    }
    /**
     * Modifica un articolo esistente con validazione.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
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
        return redirect()->back()->with('success', 'Articolo aggiornato con successo.');
    }
    /**
     * Elimina un articolo.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteArticle($id){
        $article = Article::findOrFail($id);
        $article->delete();
        return redirect()->back()->with('success', 'Articolo eliminato con successo.');
    }
    /**
     * Aggiorna lo stato MOCA di un articolo.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateArticle($id){
        $article = Article::findOrFail($id);
        $article->is_moca = !$article->is_moca;
        $article->save();
        return redirect()->back()->with('success', 'Articolo aggiornato con successo.');
    }
    /**
     * Crea un nuovo articolo con validazione.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
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
        return redirect()->back()->with('success', 'Articolo creato con successo.');
    }
    /**
     * Mostra la vista di associazione articoli-lotti.
     *
     * @return \Illuminate\View\View
     */
    public function articlesToLottos(){
        return view('admin.articles-to-lottos');
    }
    /**
     * Mostra la vista per creare un preassemblato.
     *
     * @return \Illuminate\View\View
     */
    public function createPreassembled(){
        return view('admin.create-preassembled');
    }
    /**
     * Mostra la vista per modificare un preassemblato.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function editPreassembled($id){
        $preassembleds = Preassembled::findOrFail($id);
        $articles = $preassembleds->articles;
        return view('admin.edit-preassembled', compact('preassembleds', 'articles'));
    }
    /**
     * Elimina preassemblato.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deletePreassembled($id){
        Preassembled::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Preassemblato eliminato con successo.');
    }

    /**
     * Fallback non-Livewire per creare/aggiornare un preassemblato da form POST.
     */
    public function storePreassembled(Request $request)
    {
        $validated = $request->validate([
            'preassembled_code' => 'required|string|max:255',
            'preassembled_description' => 'required|string|max:2000',
            'selected_articles' => 'required|array|min:1',
            'selected_articles.*' => 'integer|exists:articles,id',
        ]);

        $preassembleds = Preassembled::updateOrCreate([
            'code' => $validated['preassembled_code'],
        ], [
            'description' => $validated['preassembled_description'],
            'padre_description' => ' ',
            'activity' => ' ',
        ]);

        foreach ($validated['selected_articles'] as $articleId) {
            PreassembledArticle::firstOrCreate([
                'pre_assembled_id' => $preassembleds->id,
                'article_id' => $articleId,
            ]);
        }

        return redirect()->route('admin.index')->with('success', 'Preassemblato creato con successo.');
    }
}
