<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ImportUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa gli utenti da un file Excel';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $path = $this->ask("📥 Inserisci il percorso completo del file Excel degli utenti");
        $path = "C:\\Users\\softcontrol\\Documents\\ONN WATER WEB SERVER\\progetto genrazione pdf\\lista utenti.xlsx";

        if (!file_exists($path)) {
            $this->error("❌ File non trovato: $path");
            return 1;
        }

        $this->info("📂 File trovato: $path");

        try {
            $spreadsheet = IOFactory::load($path);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);
        } catch (\Throwable $e) {
            $this->error("❌ Errore nel caricamento del file: " . $e->getMessage());
            return 1;
        }

        $output = [];
        $output[] = ['name', 'operator_code', 'role', 'password'];

        array_shift($rows); // rimuove intestazione

        foreach ($rows as $index => $row) {
            $nome = trim($row['A'] ?? '');
            $cognome = trim($row['B'] ?? '');
            $matricola = trim($row['D'] ?? '');
            $ruolo = trim($row['E'] ?? '');

            if (!$nome || !$cognome || !$matricola || !$ruolo) {
                $this->warn("⏭️ Riga $index saltata per dati incompleti.");
                continue;
            }

            $fullName = "$nome $cognome";
            $password = "cambiami";

            $output[] = [$fullName, $matricola, $ruolo, $password];

            $this->line("✅ $fullName ($matricola) - $ruolo - password generata.");

            User::updateOrCreate(
                ['operator_code' => $matricola],
                [
                    'name' => $fullName,
                    'role' => $ruolo,
                    'password' => Hash::make($password),
                ]
            );

            $this->line("✅ $fullName ($matricola) - $ruolo - utente inserito nel DB.");
        }

        $this->info("\n💾 Generazione file in corso...");
        $newSheet = new Spreadsheet();
        $newSheet->getActiveSheet()->fromArray($output);

        $writer = new Xlsx($newSheet);
        $outPath = storage_path('app/private/lista-utenti-server.xlsx');
        $writer->save($outPath);

        $this->info("✅ File esportato con successo: $outPath");

        return 0;
    }
}
