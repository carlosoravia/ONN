<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AuditLog;

class AdminController extends Controller
{
    public function index(){
        $audits = AuditLog::all();
        return view('admin.index', compact('audits'));
    }

    public function editUsers(){
        $users = User::all();
        return view('admin.edit-users', compact('users'));
    }
}
