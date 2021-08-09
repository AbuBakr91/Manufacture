<?php

namespace App\Http\Controllers;

use App\Models\Logging;
use App\Models\TechnicalCards;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoggerController extends Controller
{
    public function index()
    {
        return Logging::all();
    }

    public function store(Request $request)
    {
        $log = new Logging;
        $log->log_errors =$request->error . '  тех карта: '. $request->card_id;
        $log->save();
    }

    public function destroy($id)
    {
        return DB::table('logging')->truncate();
    }
}
