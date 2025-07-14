<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;

class UsersTable extends Component
{
    public $query = '';
    public $queryNumber = '';
    public $table = true;

    public function toggleTable()
    {
        $this->table = !$this->table;
    }

    public function render()
    {
        if($this->query){
            $users = User::query()
                ->when($this->query, function ($query) {
                    $query->where('name', 'like', '%' . $this->query . '%');
                })->orderBy('id', 'desc')
                ->get();
        }else if($this->queryNumber){
            $users = User::query()
                ->when($this->queryNumber, function ($queryNumber) {
                    $queryNumber->where('operator_code', 'like', '%' . $this->queryNumber . '%');
                })->get();
        }else {
            $users = User::all();
        }
        return view('livewire.users-table', compact('users'));
    }
}
