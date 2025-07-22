<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Article;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Str;
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
        $filePath = $this->ask('Inserisci il percorso del file degli articoli');

        if (!file_exists($filePath)) {
            $this->error("❌ File non trovato: $filePath");
            return 1;
        }
        $mocaFilePath = $this->ask('moca_list');
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
        $this->line($xml->record);
        $isMoca = false;
        foreach ($xml->record as $record) {
            if (in_array((string) $record['CodiceComponente'], $mocaCodes)) {
                $isMoca = true;
            }
            try {
                Article::updateOrCreate(
                    [
                        'code' => (string) $record['CodiceComponente'],
                        'description' => (string) $record['DescCompo'],
                        'is_moca' => $isMoca
                    ]
                );

                $this->line("✅ Articolo importato: " . $record);
                $count++;
            } catch (\Throwable $e) {
                $this->error("❌ Errore articolo " . $record['code'] . ": " . $e->getMessage());
                Log::error("[ImportArticles] Errore su " . $record['code'] . ": " . $e->getMessage());
            }
        }

        $this->info("🔧 Totale articoli importati: $count");
        return 0;
    }
}
