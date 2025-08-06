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
        if (!file_exists($filePath)) {
            $this->error("❌ File non trovato: $filePath");
            return 1;
        }
        $mocaCodes = [];
        try {
            $xmlContent = file_get_contents($filePath);
            $xml = new SimpleXMLElement($xmlContent);
        } catch (\Throwable $e) {
            $this->error("❌ Errore nel parsing XML: " . $e->getMessage());
            return 1;
        }

        foreach ($xml->record as $record) {
            $code = trim((string) $record['CodiceComponente']);
            $isMoca = $record['CatOmoFiglio'] == 012 ? true : false;
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
                $this->error("❌ Errore salvataggio: " . $e->getMessage());
            }
        }
        $this->info("🟢 Totale articoli elaborati: $count");
        return 0;
    }
}
