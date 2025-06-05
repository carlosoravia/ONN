<?php
namespace App\Services;

use App\Models\Lotto;
use Carbon\Carbon;

class GenerateLottoNumberService
{
    public function generaCodiceLotto(): string
    {
        $data = Carbon::now()->format('Ymd');
        $count = Lotto::whereDate('created_at', Carbon::today())->count();
        $index = $count;
        $letter = '';
        $index++;
        while ($index > 0) {
            $index--;
            $letter = chr(65 + ($index % 26)) . $letter;
            $index = intdiv($index, 26);
        }
        return 'LP-' . $data . '-' . $letter;
    }

}

?>
