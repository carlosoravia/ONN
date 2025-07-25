<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Article;
use SimpleXMLElement;

class ImportArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-articles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        ini_set('memory_limit', '-1');

        $filePath = "/var/backups/mago/estrazione-mago.xml";
        $mocaFilePath = "/var/backups/mago/moca_list.txt";

        if (!file_exists($filePath)) {
            $this->error("❌ File non trovato: $filePath");
            return 1;
        }
        $mocaCodes = [];

        if ($mocaFilePath && file_exists($mocaFilePath)) {
            $mocaCodes = file($mocaFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $this->info("📄 Lista MOCA caricata: " . count($mocaCodes) . " codici");
        }

        try {
            $xmlContent = file_get_contents($filePath);
            $xml = new SimpleXMLElement($xmlContent);
        } catch (\Throwable $e) {
            $this->error("❌ Errore nel parsing XML: " . $e->getMessage());
            return 1;
        }
        $this->info("🔍 Totale elementi record trovati: " . count($xml->record));
        $count = 0;
        $mocaCodes = array_map('trim', file($mocaFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));

        // Prendiamo tutti i codici reali nel DB
        $dbCodes = Article::pluck('code')->map(fn($code) => strtoupper(trim($code)));

        // Normalizza anche i codici MOCA per sicurezza
        $mocaCodesNormalized = collect($mocaCodes)
            ->map(fn($code) => strtoupper(trim($code)))
            ->unique();

        // Verifica i codici presenti
        $missingCodes = $mocaCodesNormalized->diff($dbCodes);

        // Stampa il risultato
        if ($missingCodes->isNotEmpty()) {
            $this->warn('⚠️ Alcuni codici MOCA non esistono nel DB:');
            foreach ($missingCodes as $missingCode) {
                //$this->line("- {$missingCode}");
            }
            $this->info("Totale codici non trovati nel DB: " . $missingCodes->count());
        } else {
            $this->info("✅ Tutti i codici MOCA trovati nel DB.");
        }

        $mocaMatches = 0;

        foreach ($xml->record as $record) {
            $code = trim((string) $record['CodiceComponente']);
            $isMoca = in_array($code, $mocaCodes, true);

            if ($isMoca) {
                //$this->line("✅ Codice MOCA trovato: $code");
                $mocaMatches++;
            }else{
                $this->line('');
            }

            try {
                Article::updateOrCreate(
                    ['code' => $code],
                    [
                        'description' => (string) $record['DescCompo'],
                        'is_moca' => $isMoca,
                    ]
                );
                $count++;
            } catch (\Throwable $e) {
                //$this->error("❌ Errore salvataggio: " . $e->getMessage());
            }
        }

        $this->info("🎯 Totale articoli MOCA trovati: $mocaMatches");
        $this->info("🟢 Totale articoli elaborati: $count");
        return 0;
    }
}
