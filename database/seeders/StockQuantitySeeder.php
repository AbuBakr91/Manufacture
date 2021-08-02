<?php

namespace Database\Seeders;

use App\Models\ProductNames;
use App\Models\StockModel;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class StockQuantitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        date_default_timezone_set('Europe/Moscow');
        $res = json_decode(Http::withBasicAuth(env('M_LOGIN'), env('M_PASS'))->get("https://online.moysklad.ru/api/remap/1.2/report/profit/byproduct?momentFrom=" . Carbon::now()->subMonth()."&momentTo=". Carbon::now() .""));
        foreach ($res->rows as $key => $item) {
            $name_id = ProductNames::where('name', $item->assortment->name)->pluck('id')->toArray();
            $stock{$key} = StockModel::where('product_name_id', $name_id);
            $stock{$key}->update([
                "sellQuantity" => (int)$item->sellQuantity,
                "returnQuantity" => (int)$item->returnQuantity
            ]);

        }
    }
}
