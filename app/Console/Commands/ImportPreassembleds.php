<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PreAssembled;
use App\Models\Article;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

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
        ini_set('memory_limit', '-1');

        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("❌ File non trovato: $filePath");
            return 1;
        }

        $xmlContent = file_get_contents($filePath);
        $xml = new SimpleXMLElement($xmlContent);

        $struttura = [];
        $index = 0;

        foreach ($xml->record as $record) {
            $index++;

            $codePadre = trim((string) $record['CodicePadre']);
            $codeArticolo = trim((string) $record['CodiceComponente']);

            if (!$codePadre || !$codeArticolo) {
                $this->warn("⏭️ Riga $index saltata: codice padre o componente mancante");
                continue;
            }

            if (!isset($struttura[$codePadre])) {
                $struttura[$codePadre] = [
                    'description' => trim((string) $record['DescPadre']),
                    'padre_description' => trim((string) $record['DescPadre']),
                    'activity' => '',
                    'articoli' => []
                ];
            }

            $struttura[$codePadre]['articoli'][$codeArticolo] = [
                'description' => trim((string) $record['DescCompo']),
                'qty' => floatval((string) $record['Qta']) ?: 1.0
            ];
        }

        Log::debug("[ImportPreassembleds] Struttura costruita: " . json_encode($struttura));

        $this->info("🧱 Importazione preassemblati:");
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

                $this->line("✅ Preassemblato: {$preassembled->code}");

                foreach ($info['articoli'] as $code => $articolo) {
                    $article = Article::firstOrCreate(
                        ['code' => $code],
                        ['description' => $articolo['description']]
                    );

                    $exists = $preassembled->articles()->where('article_id', $article->id)->exists();
                    if (!$exists) {
                        $preassembled->articles()->attach($article->id, ['qty' => $articolo['qty']]);
                        $this->line("   ↳ Articolo collegato: {$article->code} (qty: {$articolo['qty']})");
                        $associati++;
                    } else {
                        $this->line("   ⏭️ Articolo già collegato: {$article->code}");
                    }
                }

            } catch (\Throwable $e) {
                $this->error("❌ Errore su preassemblato $codice: " . $e->getMessage());
                Log::error("[ImportPreassembleds] Errore $codice: " . $e->getMessage());
            }
        }

        $this->info("🔗 Totale collegamenti articoli/preassemblati effettuati: $associati");
        return 0;
    }
}
