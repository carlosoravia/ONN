<?php

namespace App\Console\Commands;

use \SimpleXMLElement;
use App\Models\Client;
use App\Models\Order;
use App\Models\OrderLine;
use App\Traits\XmlInterpreter;
use Illuminate\Console\Command;

class ImportSalesFromMago extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-sales-from-mago';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa ordini di vendita da Mago';
    use XmlInterpreter;
    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $filePath = "/var/backups/mago/estrazione-sales-mago.xml";
        $filePath = "C:\\Users\\softcontrol\\Documents\\ONN WATER WEB SERVER\\scripts macchina\\estrazione-sales.xml";
        if (!file_exists($filePath)) {
            $this->error("❌ File non trovato: $filePath");
            return 1;
        }
        try {
            $xml = $this->loadXml($filePath);
        } catch (\Throwable $e) {
            $this->error("❌ Errore nel parsing XML: " . $e->getMessage());
            return 1;
        }

        $ordini = 0;
        $righe = 0;

        foreach ($this->records($xml, 'record') as $record) {
            // Cliente
            $clientCode = $this->xmlStr($record, 'Cliente');
            $ragSoc     = $this->xmlStr($record, 'RagioneSociale');

           $client = Client::FirstOrCreate(
            [
                'code' => $clientCode],
                ['ragione_sociale' => $ragSoc]
            );

            // Ordine
            $orderId = $this->xmlStr($record, 'IdOrdine');
            $order = Order::FirstOrCreate(
                ['id' => $orderId],
                [
                    'num_ordine'  => $this->xmlStr($record, 'NumOrdine'),
                    'data_ordine' => $this->xmlDate($record, 'DataOrdine'),
                    'causale'     => $this->xmlStr($record, 'Causale'),
                    'client_id'   => $client->id,
                ]
            );
            $ordini++;

            $this->info("📦 Ordine importato: {$order->id} ({$order->num_ordine}) per cliente {$client->ragione_sociale}");

            // Riga ordine
            $articleId = $this->xmlStr($record, 'Articolo');
            $line = OrderLine::updateOrCreate(
                [
                    'order_id'   => $order->id,
                    'article_id' => $articleId,
                ],
                [
                    'quantita'   => $this->xmlFloat($record, 'Qta', 0),
                    'um'         => $this->xmlStr($record, 'UM'),
                    'data_cons_prevista' => $this->xmlDate($record, 'DataConsPrevista'),
                ]
            );
            $righe++;

            $this->line("   ↳ Riga: articolo {$articleId}, qty {$line->quantita}, um {$line->um}");
        }

        $this->newLine();
        $this->info("✅ Import concluso: {$ordini} ordini e {$righe} righe processate");

        return 0;
    }
}
