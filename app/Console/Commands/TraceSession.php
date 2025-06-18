<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;


class TraceSession extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:trace-session';

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
        DB::listen(function ($query) {
            if (stripos($query->sql, 'from `sessions`') !== false) {
                logger()->error('🔍 QUERY SULLA TABELLA sessions:', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time
                ]);
            }
        });

        $this->info('In ascolto delle query... (Ctrl+C per uscire)');
        while (true) {
            sleep(1); // loop continuo per test
        }
    }
}
