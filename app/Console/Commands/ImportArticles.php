<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Article;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Str;


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
        $fileArticoli = $this->ask('Inserisci il percorso del file degli articoli');
        $fileMoca = $this->ask('Inserisci il percorso del file MOCA');

        // Importa articoli
        if (!file_exists($fileArticoli)) {
            $this->error("File articoli non trovato: $fileArticoli");
            return 1;
        }

        $this->info('Importazione articoli in corso...');
        $spreadsheet = IOFactory::load($fileArticoli);
        $sheet = $spreadsheet->getActiveSheet();

        $rows = $sheet->toArray(null, true, true, true);
        $intestazioni = array_shift($rows);

        foreach ($rows as $row) {
            $code = trim($row['A'] ?? '');
            $description = trim($row['B'] ?? '');

            if ($code) {
                $articolo = Article::firstOrNew(['code' => $code]);
                $articolo->description = $description;
                $articolo->save();
                $this->line("[✔] Articolo importato/aggiornato: $code");
            }
        }

        // Aggiorna is_moca
        if (!file_exists($fileMoca)) {
            $this->error("File MOCA non trovato: $fileMoca");
            return 1;
        }

        $this->info('Aggiornamento campo is_moca in corso...');
        $spreadsheet = IOFactory::load($fileMoca);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);
        $intestazioni = array_shift($rows);

        foreach ($rows as $row) {
            $code = trim($row['A'] ?? '');
            $articolo = Article::where('code', $code)->first();

            if ($articolo) {
                $articolo->is_moca = 1;
                $articolo->save();
                $this->line("[✔] MOCA aggiornato: $code");
            }
        }

        $this->info('Importazione completata con successo.');
        return 0;
    }
}
