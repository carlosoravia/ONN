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

        $path = $this->ask('Inserisci il percorso completo del file Excel');

        if (!file_exists($path)) {
            $this->error("\n❌ File non trovato: {$path}");
            Log::error("[ImportPreassembleds] File non trovato: {$path}");
            return 1;
        }

        $this->info("\n✅ File trovato: {$path}\n");

        try {
            $spreadsheet = IOFactory::load($path);
        } catch (\Throwable $e) {
            $this->error("Errore nel caricamento del file Excel: " . $e->getMessage());
            return 1;
        }

        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        Log::debug("[ImportPreassembleds] Numero righe caricate: " . count($rows));
        $this->info("\n📥 Righe totali nel foglio: " . count($rows));

        array_shift($rows); // rimuove intestazione

        $struttura = [];

        foreach ($rows as $index => $row) {
            $codePadre = trim($row['A'] ?? '');
            $codeArticolo = trim($row['E'] ?? '');

            if (!$codePadre || !$codeArticolo) {
                $this->warn("⏭️ Riga $index saltata: codice padre o articolo mancante");
                Log::debug("[RIGA SALTATA] Index $index - Padre: '$codePadre', Articolo: '$codeArticolo'");
                continue;
            }

            $this->line("📄 Riga $index - Padre: $codePadre, Articolo: $codeArticolo");

            if (!isset($struttura[$codePadre])) {
                $struttura[$codePadre] = [
                    'description' => trim($row['F'] ?? ''),
                    'padre_description' => trim($row['B'] ?? ''),
                    'activity' => '',
                    'articoli' => []
                ];
            }

            $struttura[$codePadre]['articoli'][$codeArticolo] = trim($row['H'] ?? '-');
        }

        Log::debug("[ImportPreassembleds] Struttura costruita: " . json_encode($struttura));
        $this->info("\n🧱 Riepilogo struttura dati:");
        foreach ($struttura as $padre => $info) {
            $this->line("🔧 {$padre} - {$info['description']} ({$info['activity']})");
            foreach ($info['articoli'] as $artCode => $desc) {
                $this->line("   ↪️ $artCode - $desc");
            }
        }

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
                Log::info("[ImportPreassembleds] Preassemblato: {$preassembled->code}");

                foreach ($info['articoli'] as $code => $descrizione) {
                    $article = Article::firstOrCreate(
                        ['code' => $code],
                        ['description' => $descrizione]
                    );

                    $exists = $preassembled->articles()->where('article_id', $article->id)->exists();
                    if (!$exists) {
                        $preassembled->articles()->attach($article->id);
                        $this->line("   ↳ Articolo collegato: {$article->code} → {$preassembled->code}");
                        $associati++;
                    } else {
                        $this->line("   ⏭️ Articolo già collegato: {$article->code}");
                    }
                }
            } catch (\Throwable $e) {
                $this->error("❌ Errore durante l'importazione del preassemblato {$codice}: " . $e->getMessage());
                Log::error("[ImportPreassembleds] Errore preassemblato {$codice}: " . $e->getMessage());
            }
        }

        $this->info("\n✅ Importazione completata con successo. Articoli associati: {$associati}");
        return 0;
    }
}
