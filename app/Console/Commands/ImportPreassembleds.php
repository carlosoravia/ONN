<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PreAssembled;
use App\Models\Article;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;
use Illuminate\Support\Facades\DB;
class ImportPreassembleds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-preassembleds';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa preassemblati e articoli associati da un file Excel';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $filePath = "/var/backups/mago/estrazione-mago.xml";

        if (!file_exists($filePath)) {
            $this->error("❌ File non trovato: $filePath");
            return 1;
        }

        try {
            $xmlContent = file_get_contents($filePath);
            $xml = new SimpleXMLElement($xmlContent);
        } catch (\Throwable $e) {
            $this->error("❌ Errore XML: " . $e->getMessage());
            return 1;
        }

        $struttura = [];
        $index = 0;

        foreach ($xml->record as $record) {
            $index++;

            $padre = trim((string) $record['CodicePadre']);
            $componente = trim((string) $record['CodiceComponente']);

            if (!$padre || !$componente) {
                $this->warn("⏭️ Riga $index saltata: Codice padre o componente mancante");
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

        $this->info("\n🔍 Trovati " . count($struttura) . " preassemblati nel file XML.");
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

                $this->line("\n✅ Preassemblato: {$preassembled->code}");

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

                    $this->line("   ↪ Articolo {$article->code} collegato (ordine: {$dati['order']})");
                    $associati++;
                }
            } catch (\Throwable $e) {
                $this->error("❌ Errore durante import preassemblato {$codice}: " . $e->getMessage());
                Log::error("[ImportPreassembleds] Errore su {$codice}: " . $e->getMessage());
            }
        }

        $this->info("\n🔗 Totale articoli associati: {$associati}");
        return 0;
    }
}
