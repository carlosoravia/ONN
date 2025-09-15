<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InspectSqlSrvView extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:inspect-view';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
         $this->info("Inizio sincronizzazione articoli...");

        // Leggo dati dalla vista remota
        $rows = DB::connection('sqlsrv')->table('CI_BOM')->first();
        dd($rows->DescCatPadre);

         $this->info("Sincronizzazione completata.");
        return 0;
    }
}
