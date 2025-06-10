<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AuditLog;
use App\Models\Lotto;

class AdminController extends Controller
{
    public function index(){
        $audits = AuditLog::all();
        $lottos = Lotto::where('created_at', '>=', now()->subDays(30))->get();
        $lastLotto = Lotto::orderBy('created_at', 'desc')->first();
        $lottosCount = $lottos->count();
        if (empty($audits)) {
            return view('admin.index', ['audits' => []]);
        }
        return view('admin.index', compact('audits', 'lastLotto', 'lottosCount'));
    }

    public function editUsers(){
        $users = User::all();
        if (empty($users)) {
            return view('admin.edit-users', ['users' => []]);
        }
        return view('admin.edit-users', compact('users'));
    }
}
