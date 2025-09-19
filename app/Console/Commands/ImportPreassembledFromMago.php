<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Preassembled;
use App\Models\PreassembledArticle;
use App\Traits\XmlInterpreter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use SimpleXMLElement;

class ImportPreassembledFromMago extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-preassembled-from-mago';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa articoli e preassemblati da Mago';
    use XmlInterpreter;
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info("Inizio import preassemblati...");
        $start = microtime(true);
        $insertedPreassembled = 0;
        $updatedPreassembled = 0;
        $insertedArticles = 0;
        $updatedArticles = 0;
        $pivotCount = 0;

        // 1. Leggiamo dal remoto
        $rows = DB::connection('sqlsrv')->table('CI_BOM')->get();

        if ($rows->isEmpty()) {
            $this->warn("⚠ Nessun dato trovato in CI_BOM");
            return 0;
        }

        // Carica codici esistenti in memoria
        $existingPreassembled = Preassembled::all()->keyBy('code');
        $existingArticles = Article::all()->keyBy('code');

        foreach ($rows as $row) {
            // 2. Articolo padre (preassembled)
            if (isset($existingPreassembled[$row->CodicePadre])) {
                $preassembled = $existingPreassembled[$row->CodicePadre];
                $preassembled->update([
                    'description'       => $row->DescPadre,
                    'padre_description' => $row->DescCatPadre,
                    'activity'          => ' ',
                ]);
                $updatedPreassembled++;
            } else {
                $preassembled = Preassembled::create([
                    'code'              => $row->CodicePadre,
                    'description'       => $row->DescPadre,
                    'padre_description' => $row->DescCatPadre,
                    'activity'          => ' ',
                ]);
                $existingPreassembled[$row->CodicePadre] = $preassembled;
                $insertedPreassembled++;
            }

            // 3. Articolo figlio (article)
            if (isset($existingArticles[$row->CodiceComponente])) {
                $article = $existingArticles[$row->CodiceComponente];
                // Aggiorna sempre senza errori se già esiste
                $article->update([
                    'description'       => $row->DescCompo,
                    'padre_description' => $row->DescCatFiglio,
                ]);
                $updatedArticles++;
            } else {
                $article = Article::create([
                    'code'              => $row->CodiceComponente,
                    'description'       => $row->DescCompo,
                    'padre_description' => $row->DescCatFiglio,
                ]);
                $existingArticles[$row->CodiceComponente] = $article;
                $insertedArticles++;
            }

            // 4. Relazione pivot
            PreassembledArticle::updateOrCreate(
                [
                    'pre_assembled_id' => $preassembled->id,
                    'article_id'       => $article->id,
                ],
                [
                    'order'     => $row->NumRiga,
                ]
            );
            $pivotCount++;
        }

        $duration = round(microtime(true) - $start, 2);
        $this->info("✅ Import completato con successo.");
        $this->info("Preassemblati inseriti: $insertedPreassembled, aggiornati: $updatedPreassembled");
        $this->info("Articoli inseriti: $insertedArticles, aggiornati: $updatedArticles");
        $this->info("Relazioni pivot elaborate: $pivotCount");
        $this->info("Tempo impiegato: {$duration}s");
        return 0;
    }
}
