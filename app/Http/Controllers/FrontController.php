<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lotto;
use App\Models\Preassembled;
class FrontController extends Controller
{
    public function showTodayLottos(){
        $preassembleds = [];
        $lottos = [];
        $lottos = Lotto::where('created_at', '>=', date_format(now(), 'Y-m-d'))->orderBy('id', 'DESC')->get();
        foreach ($lottos as $lotto) {
            array_push($preassembleds, Preassembled::where('id', $lotto->pre_assembled_id)->get());
        }
        return view('front.today-lottos', compact('lottos', 'preassembleds'));
    }

    public function downloadLotto($filename)
    {
        $filePath = storage_path('app/public/lottos/' . $filename);

        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        return response()->download($filePath);
    }
}
