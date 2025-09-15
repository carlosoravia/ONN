<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Preassembled;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use SimpleXMLElement;
use App\Traits\XmlInterpreter;
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
    public function handle()
    {
        // $filePath = "/var/backups/mago/estrazione-mago.xml";
        $filePath = $this->ask('Inserisci il percorso del file XML:');
        if (!file_exists($filePath)) {
            $this->error("❌ File non trovato: $filePath");
            return 1;
        }
        try {
            $xml = $this->loadXml($filePath);
        } catch (\Throwable $e) {
            $this->error("❌ Errore nel parsing XML: " . $e->getMessage());
            return 1;
        }

        // 2) Primo passaggio: articoli + MOCA
        $count = 0;
        $mocaCount = 0;

        foreach ($this->records($xml, 'record') as $record) {
            $code   = $this->xmlStr($record, 'CodiceComponente');     // es. "010104-1-F375"
            if (!$code) { continue; }

            $isMoca = $this->xmlStr($record, 'CatOmoFiglio') === '012'; // tua logica MOCA

            try {
                Article::updateOrCreate(
                    ['code' => $code],
                    [
                        'description' => $this->xmlStr($record, 'DescCompo'),
                        'is_moca'     => $isMoca,
                    ]
                );
                $count++;
                if ($isMoca) { $mocaCount++; }
                //$this->line("✔ Articolo importato: {$article->code} ({$article->description})" . ($isMoca ? " [MOCA]" : ""));
            } catch (\Throwable $e) {
                $this->error("❌ Errore salvataggio articolo {$code}: " . $e->getMessage());
            }
        }

        $this->info("Totale articoli elaborati: {$count}");
        $this->info("Totale articoli MOCA: {$mocaCount}");

        // 3) Secondo passaggio: costruzione struttura Preassembled -> Articoli (con order)
        $struttura = [];
        $index = 0;

        foreach ($this->records($xml, 'record') as $record) {
            $index++;

            $padre      = $this->xmlStr($record, 'CodicePadre');       // es. "A00-MO-90-150-3-I5-B"
            $componente = $this->xmlStr($record, 'CodiceComponente');  // es. "010104-1-F375"
            if (!$padre || !$componente) {
                $this->warn("⏭ Riga {$index} saltata: Codice padre o componente mancante");
                continue;
            }

            if (!isset($struttura[$padre])) {
                $struttura[$padre] = [
                    'description'       => $this->xmlStr($record, 'DescPadre'),
                    'padre_description' => $this->xmlStr($record, 'DescPadre'),
                    'activity'          => '',                  // rimane vuota come nel tuo file
                    'articoli'          => []
                ];
            }

            $struttura[$padre]['articoli'][$componente] = [
                'description' => $this->xmlStr($record, 'DescCompo'),
                'order'       => $this->xmlInt($record, 'NumRiga', 0),
                // 'qty'      => $this->xmlFloat($record, 'Qta', 0),
                // 'um'       => $this->xmlStr($record, 'UM'),
            ];
        }

        $this->info("\nTrovati " . count($struttura) . " preassemblati nel file XML.");
        $associati = 0;

        // 4) Upsert Preassembled + pivot preassembled_articles
        foreach ($struttura as $codice => $info) {
            try {
                $pre = Preassembled::updateOrCreate(
                    ['code' => $this->xmlStr($record, 'CodicePadre')],
                    [
                        'description'       => $this->xmlStr($record, 'DescPadre'),
                        'padre_description' => $this->xmlStr($record, 'DescPadre'),
                        'activity'          => '',
                    ]
                );
                //$this->info("➕ Preassemblato: {$pre->code} - {$pre->description}");
                foreach ($info['articoli'] as $artCode => $dati) {
                    $article = Article::updateOrCreate(
                        ['code' => $this->xmlStr($record, 'CodiceComponente')],
                        [
                            'description' => $this->xmlStr($record, 'DescCompo'),
                            'is_moca'     => $this->xmlStr($record, 'CatOmoFiglio') === '012',
                        ]
                    );

                    DB::table('preassembled_articles')->updateOrInsert(
                        [
                            'pre_assembled_id' => $pre->id,      // nome campo come nel tuo file
                            'article_id'       => $article->id,
                        ],
                        [
                            'order'      => (int) ($dati['order'] ?? 0),
                            'updated_at' => now(),
                            'created_at' => now(),
                            // Se in futuro aggiungi qty/um nella pivot:
                            // 'qty'      => $dati['qty'] ?? 0,
                            // 'um'       => $dati['um'] ?? null,
                        ]
                    );
                    $associati++;
                    //$this->line("   ↳ collegato articolo {$article->code} ({$dati['description']}) con order {$dati['order']}");
                }
            } catch (\Throwable $e) {
                $this->error("❌ Errore durante import preassemblato {$codice}: " . $e->getMessage());
            }
        }

        $this->info("Associazioni preassemblato↔articolo create/aggiornate: {$associati}");
        return 0;
    }
}
