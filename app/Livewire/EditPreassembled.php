<?php

namespace App\Livewire;

use App\Models\Article;
use App\Models\PreassembledArticle;
use Livewire\Component;

class EditPreassembled extends Component
{
    public $preassembled_id;
    public $articles;
    public function removeArticle($id){
        PreassembledArticle::where('article_id', $id)->delete();

        $this->articles = Article::whereHas('preassembleds', function ($query) {
            $query->where('pre_assembled_id', $this->preassembled_id);
        })->get();
    }

    public
    public function render()
    {
        return view('livewire.edit-preassembled');
    }
}
