<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AuditLog;
use App\Models\Lotto;
use Illuminate\Support\Facades\DB;
class AdminController extends Controller
{
    public function index(){
        $audits = AuditLog::orderBy('created_at', 'desc')->take(20)->get();
        $lottos = Lotto::where('created_at', '>=', date_format(now(), 'Y-m-d'))->get();
        $lastLotto = Lotto::orderBy('created_at', 'desc')->first();
        $lottosCount = $lottos->count();
        return view('admin.index', compact('audits', 'lastLotto', 'lottosCount'));
    }

    public function editUsers(){
        $users = User::all();
        if (empty($users)) {
            return view('admin.edit-users', ['users' => []]);
        }
        return view('admin.edit-users', compact('users'));
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
        User::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Utente eliminato con successo.');
    }

    public function makeAdmin($id)
    {
        $user = User::findOrFail($id);
        $user->role = 'admin';
        $user->save();

        return redirect()->back()->with('success', 'Ruolo impostato su Admin.');
    }

    public function makeOperator($id)
    {
        $user = User::findOrFail($id);
        $user->role = 'operator';
        $user->save();

        return redirect()->back()->with('success', 'Ruolo impostato su Operatore.');
    }

}
