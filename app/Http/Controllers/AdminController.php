<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AuditLog;
use App\Models\Lotto;

class AdminController extends Controller
{
    public function index(){
        $audits = AuditLog::all()->sortByDesc('created_at')->take(20);
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
        $audit = AuditLog::findOrFail($id);
        $record = $audit->getTargetRecord();
        $user = $audit->user;
        $updatedData = $audit->changed_data;
        return view('admin.show-audit-logs', compact('audit','record', 'user', 'updatedData'));
    }
}
