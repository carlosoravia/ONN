<?php

namespace App\Livewire;

use App\Models\Article;
use App\Models\PreassembledArticle;
use Livewire\Component;
use App\Models\PreAssembled;
class SubmitPreassembled extends Component
{
    // IDs selezionati (stato sorgente affidabile per Livewire)
    public array $selectedArticleIds = [];
    // Modelli per la vista (derivati dagli ID)
    public $selectedArticles = [];
    public $query = '';
    public $preassembled_code = '';
    public $preassembled_description = '';
    public $table = true;
    public bool $showConfirmModal = false;

    public function toggleTable()
    {
        $this->table = !$this->table;
    }
    public function mount(){
    }
    public function addArticle($id){
        if (!in_array((int)$id, $this->selectedArticleIds, true)) {
            $this->selectedArticleIds[] = (int)$id;
        }
    }

    public function removeArticle($id){
        $this->selectedArticleIds = array_values(array_filter($this->selectedArticleIds, function($v) use ($id) {
            return (int)$v !== (int)$id;
        }));
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
            'preassembled_code' => 'required|string|max:255',
            'preassembled_description' => 'required|string|max:2000',
            'selectedArticleIds' => 'required|array|min:1',
            'selectedArticleIds.*' => 'exists:articles,id',
        ];
    }

    // Computed property: il bottone si mostra solo quando i campi sono popolati e c'è almeno un articolo
    public function getCanSubmitProperty(): bool
    {
        $codeOk = is_string($this->preassembled_code) && trim($this->preassembled_code) !== '';
        $descOk = is_string($this->preassembled_description) && trim($this->preassembled_description) !== '';
        $hasArticles = is_array($this->selectedArticleIds) && count($this->selectedArticleIds) > 0;
        return $codeOk && $descOk && $hasArticles;
    }

    // Step 1: valida e mostra modale di conferma
    public function submit(){
        $this->validate();
        $this->showConfirmModal = true;
    }

    // Step 2: conferma, salva e reindirizza
    public function confirmSubmit(){
        $preAssembled = PreAssembled::updateOrCreate([
            'code' => $this->preassembled_code,
        ],[
            'description' => $this->preassembled_description,
            'padre_description' => ' ',
            'activity' => ' ',
        ]);

        foreach ($this->selectedArticles as $sa) {
            if (!isset($sa->id)) { continue; }
            PreassembledArticle::firstOrCreate([
                'pre_assembled_id' => $preAssembled->id,
                'article_id' => $sa->id,
            ]);
        }

        $this->showConfirmModal = false;
        session()->flash('success', 'Preassemblato creato con successo.');
        // Evita redirect server-side durante un update Livewire: naviga lato client
        $this->dispatch('navigate-to', url: route('admin.index'));
        return;
    }

    public function cancelSubmit(){
        $this->showConfirmModal = false;
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

        // Aggiorna i modelli selezionati per la vista a partire dagli ID (affidabile in v3)
        $this->selectedArticles = Article::whereIn('id', $this->selectedArticleIds)->get();

        return view('livewire.submit-preassembled', compact('articles'));
    }
}
