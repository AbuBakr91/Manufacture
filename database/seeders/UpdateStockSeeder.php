<?php

namespace Database\Seeders;

use App\Models\ProductNames;
use App\Models\StockModel;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class UpdateStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        date_default_timezone_set('Europe/Moscow');

        $stocks = json_decode(Http::withBasicAuth(env('M_LOGIN'), env('M_PASS'))->get('https://online.moysklad.ru/api/remap/1.2/report/stock/all'));
        foreach ($stocks->rows as $key => $item) {
            $name_id = ProductNames::where('name', $item->name)->pluck('id')->toArray();
            $stock = StockModel::where('product_name_id', $name_id);
            $stock->update([
                "stock" => (int)$item->stock,
                "reserve" => (int)$item->reserve
            ]);

        }
    }
}
