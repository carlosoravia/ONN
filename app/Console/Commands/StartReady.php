<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class StartReady extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:start-ready';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pulizia cache e avvio scheduler Laravel';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->callSilent('config:clear');
        $this->callSilent('route:clear');
        $this->callSilent('view:clear');
        $this->callSilent('cache:clear');

        $this->call('config:cache');
        $this->call('route:cache');
        $this->call('view:cache');

        $this->info('✅ Laravel ottimizzato. Avvio scheduler...');
        $this->call('schedule:run');
    }
}
