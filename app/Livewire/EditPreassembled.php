<?php

namespace App\Livewire;

use App\Models\Article;
use App\Models\PreassembledArticle;
use Livewire\Component;

class EditPreassembled extends Component
{
    public $preassembled_id;
    public $articles;
    public $preAssembled;
    public function removeArticle($id){
        PreassembledArticle::where('article_id', $id)->delete();

        Article::whereHas('preassembleds', function ($query) {
            $query->where('pre_assembled_id', $this->preassembled_id);
        })->get();
    }

    public function render()
    {
        $this->articles = Article::whereHas('preassembleds', function ($query) {
            $query->where('pre_assembled_id', $this->preAssembled->id);
        })->get();

        return view('livewire.edit-preAssembled');
    }
}
