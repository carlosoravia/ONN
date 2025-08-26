<?php

namespace App\Livewire;

use App\Models\Article;
use App\Models\PreassembledArticle;
use Livewire\Component;
use App\Models\PreAssembled;
class SubmitPreassembled extends Component
{
    //public $articles;
    public $selectedArticles = [];
    public $query = '';
    public $preassembled_code = '';
    public $preassembled_description = '';
    public $table = true;

    public function toggleTable()
    {
        $this->table = !$this->table;
    }
    public function mount(){
    }
    public function addArticle($id){
        $article = Article::find($id);
        array_push($this->selectedArticles, $article);
    }

    public function removeArticle($id){
        $this->selectedArticles = array_filter($this->selectedArticles, function($a) use ($id) {
            return $a->id != $id;
        });
    }
    public function search(){
        $articles = Article::query()
            ->when($this->query, function ($query) {
                $query->where('code', 'like', '%' . $this->query . '%');
            })->orderBy('id', 'desc')
            ->get();
        return $articles;
    }
    public function rules(){
        return [
            'selectedArticles' => 'required|array|min:1',
            'selectedArticles.*.id' => 'exists:articles,id',
        ];
    }

    public function submit(){
        $preassembled = Preassembled::updateOrCreate([
            'code' => $this->preassembled_code,
        ],[
            'description' => $this->preassembled_description,
            'padre_description' => " ",
            'activity' => " "
        ]);
        foreach ($this->selectedArticles as $sa) {
            PreassembledArticle::create([
                'pre_assembled_id' => $preassembled->id,
                'article_id' => $sa->id,
            ]);
        }
        redirect('/admin-dashboard')->with('success', 'Preassemblato creato con successo.');
    }
    public function render()
    {
        if ($this->query) {
            $articles = Article::query()
            ->when($this->query, function ($query) {
                $query->where('code', 'like', '%' . $this->query . '%');
            })
            ->get();
        } else {
            $articles = Article::all();
        }
        return view('livewire.submit-preassembled', compact('articles'));
    }
}
