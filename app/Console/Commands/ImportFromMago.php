<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\PreAssembled;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use SimpleXMLElement;

class ImportFromMago extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-from-mago';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa articoli e preassemblati da Mago';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //ini_set('memory_limit', '-1');
        $filePath = "/var/backups/mago/estrazione-mago.xml";
        // $filePath = $this->ask('Inserisci il percorso del file XML:');
        if (!file_exists($filePath)) {
            $this->error("❌ File non trovato: $filePath");
            return 1;
        }
        try {
            $xmlContent = file_get_contents($filePath);
            $xml = new SimpleXMLElement($xmlContent);
        } catch (\Throwable $e) {
            $this->error("❌ Errore nel parsing XML: " . $e->getMessage());
            return 1;
        }
        $count = 0;
        $mocaCount = 0;
        foreach ($xml->record as $record) {
            $code = trim((string) $record['CodiceComponente']);
            $isMoca = $record['CatOmoFiglio'] == "012" ? true : false;
            try {
                Article::updateOrCreate(
                    ['code' => $code],
                    [
                        'description' => (string) $record['DescCompo'],
                        'is_moca' => $isMoca,
                    ]
                );
                $count++;

                if($isMoca){
                    $mocaCount++;
                }
            } catch (\Throwable $e) {
                $this->error("❌ Errore salvataggio: " . $e->getMessage());
            }
        }
        $this->info("Totale articoli elaborati: $count");
        $this->info("Totale articoli MOCA: $mocaCount");

        $struttura = [];
        $index = 0;

        foreach ($xml->record as $record) {
            $index++;

            $padre = trim((string) $record['CodicePadre']);
            $componente = trim((string) $record['CodiceComponente']);

            if (!$padre || !$componente) {
                $this->warn("⏭ Riga $index saltata: Codice padre o componente mancante");
                continue;
            }

            if (!isset($struttura[$padre])) {
                $struttura[$padre] = [
                    'description' => trim((string) $record['DescPadre']),
                    'padre_description' => trim((string) $record['DescPadre']),
                    'activity' => '',
                    'articoli' => []
                ];
            }

            $struttura[$padre]['articoli'][$componente] = [
                'description' => trim((string) $record['DescCompo']),
                'order' => intval((string) $record['NumRiga']) ?: 0
            ];
        }

        $this->info("\n Trovati " . count($struttura) . " preassemblati nel file XML.");
        $associati = 0;

        foreach ($struttura as $codice => $info) {
            try {
                $preassembled = PreAssembled::updateOrCreate(
                    ['code' => $codice],
                    [
                        'description' => $info['description'],
                        'padre_description' => $info['padre_description'],
                        'activity' => $info['activity'],
                    ]
                );

                foreach ($info['articoli'] as $artCode => $dati) {
                    $article = Article::firstOrCreate(
                        ['code' => $artCode],
                        ['description' => $dati['description']]
                    );

                    DB::table('preassembled_articles')->updateOrInsert(
                        [
                            'pre_assembled_id' => $preassembled->id,
                            'article_id' => $article->id
                        ],
                        [
                            'order' => $dati['order'],
                            'updated_at' => now(),
                            'created_at' => now(),
                        ]
                    );
                    $associati++;
                }
            } catch (\Throwable $e) {
                $this->error("❌ Errore durante import preassemblato {$codice}: " . $e->getMessage());
            }
        }
        return 0;
    }
}
