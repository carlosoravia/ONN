<?php

namespace App\Console\Commands;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class FixPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processa gli utenti e verifica le password, convertendole in Bcrypt se necessario';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Inizio scansione utenti...');
        $count = 2;
        $users = User::all();
        $path = storage_path('app/private/lista-utenti-server.xlsx');
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        foreach ($users as $user) {
            $sheet->setCellValue('A' . $count, $user->name);
            $sheet->setCellValue('B' . $count, $user->operator_code);
            $sheet->setCellValue('C' . $count, $user->password);
            $info = Hash::info($user->password);
            if ($info['algoName'] !== 'bcrypt') {
                $this->warn("⚠️  Password non Bcrypt per: {$user->name}");

                $user->password = Hash::make($user->password);
                $user->save();
            }
            $count++;
        }
        $writer->save($path);
        $this->info("✅ Completato: {$count} utenti aggiornati.");
    }
}
