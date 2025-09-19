<?php

namespace App\Console\Commands;

use App\Models\SalesArticle;
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
        $start = microtime(true);
        $start = microtime(true);
        $ordini = 0;
        $righe = 0;
        $insertedClients = 0;
        $updatedClients = 0;
        $insertedArticles = 0;
        $updatedArticles = 0;

        $existingClients = Client::all()->keyBy('code');
        $existingArticles = SalesArticle::all()->keyBy('id');

        $rows = \DB::connection('sqlsrv')->table('CI_Ordinato')->get();
        if ($rows->isEmpty()) {
            $this->warn("⚠ Nessun dato trovato in CI_Ordinato");
            return 0;
        }

        foreach ($rows as $row) {
            // Cliente
            $clientCode = $row->Cliente;
            $ragSoc     = $row->RagioneSociale;
            if (isset($existingClients[$clientCode])) {
                $client = $existingClients[$clientCode];
                if ($client->ragione_sociale !== $ragSoc) {
                    $client->update(['ragione_sociale' => $ragSoc]);
                    $updatedClients++;
                }
            } else {
                $client = Client::create([
                    'code' => $clientCode,
                    'ragione_sociale' => $ragSoc
                ]);
                $existingClients[$clientCode] = $client;
                $insertedClients++;
            }

            // SalesArticle (articolo di vendita)
            $articleId = strtoupper(trim($row->Articolo));
            $desc = $row->Descrizione;
            $catOmogenea = $row->CatOmogenea;
            $descCat = $row->DescCat;
            $reparto = $row->Reparto;
            $natura = $row->Natura;
            if (isset($existingArticles[$articleId])) {
                $article = $existingArticles[$articleId];
                $article->update([
                    'descrizione' => $desc,
                    'cat_omogenea' => $catOmogenea,
                    'desc_cat' => $descCat,
                    'reparto' => $reparto,
                    'natura' => $natura,
                ]);
                $updatedArticles++;
            } else {
                $article = SalesArticle::create([
                    'id' => $articleId,
                    'descrizione' => $desc,
                    'cat_omogenea' => $catOmogenea,
                    'desc_cat' => $descCat,
                    'reparto' => $reparto,
                    'natura' => $natura,
                ]);
                $existingArticles[$articleId] = $article;
                $insertedArticles++;
            }

            // Ordine
            $orderId = $row->IdOrdine;
            $order = Order::firstOrCreate(
                ['mago_id' => $row->IdOrdine],
                [
                    'mago_id'     => $row->IdOrdine,
                    'num_ordine'  => $row->NumOrdine,
                    'data_ordine' => $row->DataOrdine,
                    'causale'     => $row->Causale,
                    'client_id'   => $client->id,
                ]
            );
            $ordini++;

            // Riga ordine
            $line = OrderLine::updateOrCreate(
                [
                    'order_id'   => $order->id,
                    'article_id' => $articleId,
                ],
                [
                    'quantita'   => $row->Qta,
                    'um'         => $row->UM,
                    'data_cons_prevista' => $row->DataConsPrevista,
                ]
            );
            $righe++;
        }
        $duration = round(microtime(true) - $start, 2);
        $this->newLine();
        $this->info("✅ Import concluso: {$ordini} ordini e {$righe} righe processate");
        $this->info("Clienti inseriti: $insertedClients, aggiornati: $updatedClients");
        $this->info("Articoli inseriti: $insertedArticles, aggiornati: $updatedArticles");
        $this->info("Tempo impiegato: {$duration}s");
        return 0;
    }
}
