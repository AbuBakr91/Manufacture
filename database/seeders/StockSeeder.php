<?php

namespace Database\Seeders;

use App\Models\ProductNames;
use App\Models\StockModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class StockSeeder extends Seeder
{
    /**
     * @param $name
     * @param $products
     * @return int
     */
    protected function getSellQuantity($name, $products)
    {
        foreach ($products->rows as $row) {
            if ($row->assortment->name === $name) {
                return (int)$row->sellQuantity;
            }
        }
    }

    /**
     * @param $name
     * @param $products
     * @return int
     */
    protected function returnQuantity($name, $products)
    {
        foreach ($products->rows as $row) {
            if ($row->assortment->name === $name) {
                return (int)$row->returnQuantity;
            }
        }
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $stocks = json_decode(Http::withBasicAuth(env('M_LOGIN'), env('M_PASS'))->get('https://online.moysklad.ru/api/remap/1.2/report/stock/all'));
        $byproduct = json_decode(Http::withBasicAuth(env('M_LOGIN'), env('M_PASS'))->get('https://online.moysklad.ru/api/remap/1.2/report/profit/byproduct'));

        foreach ($stocks->rows as $key => $row) {
            $model{$key} = new StockModel;
            $model{$key}->product_name_id = ProductNames::where('name', $row->name)->pluck('id')->toArray();
            $model{$key}->stock = (int)$row->stock;
            $model{$key}->reserve = (int)$row->reserve;
            $model{$key}->sellQuantity = $this->getSellQuantity($row->name, $byproduct);
            $model{$key}->returnQuantity = $this->returnQuantity($row->name, $byproduct);
            $model{$key}->save();
        }
    }
}
