<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Article;

class ArticlesTable extends Component
{
    public $query = '';
    public $queryDescription = '';
    public $table = true;
    public function toggleTable()
    {
        $this->table = !$this->table;
    }
    public function render()
    {
        if($this->query){
            $articles = Article::query()
                ->when($this->query, function ($query) {
                    $query->where('code', 'like', '%' . $this->query . '%');
                })->orderBy('id', 'desc')
                ->get();
        }else if($this->queryDescription){
            $articles = Article::query()
                ->when($this->queryNumber, function ($queryNumber) {
                    $queryNumber->where('description', 'like', '%' . $this->queryNumber . '%');
                })->get();
        }else {
            $articles = Article::all();
        }
        return view('livewire.articles-table', compact('articles'));
    }
}
