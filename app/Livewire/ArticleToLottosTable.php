<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Article;
class ArticleToLottosTable extends Component
{
    public $query = '';
    public $lottos = [];
    public ?string $code = null;


    public function search()
    {
        $article = Article::with('lottos')->where('code', $this->code)->first();

        if ($article) {
            $this->lottos = $article->lottos;
        } else {
            $this->lottos = [];
            session()->flash('error', 'Articolo non trovato.');
        }
    }
    public function render()
    {
        return view('livewire.article-to-lottos-table');
    }
}
