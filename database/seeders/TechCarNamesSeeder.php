<?php

namespace Database\Seeders;

use App\Models\ProductNames;
use App\Models\Products;
use App\Models\TechCardProductName;
use App\Models\TechnicalCards;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class TechCarNamesSeeder extends Seeder
{
    /**
     * @param $href
     * @return mixed
     */
    private function getProduct_id($href)
    {
        $response = json_decode(Http::withBasicAuth(env('M_LOGIN'), env('M_PASS'))->get("$href"));

        return $response->name;
    }


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = json_decode(Products::select('product', 'card_id')->get());
        $output = [];

        foreach ($products as $product) {
            $id[] = TechnicalCards::select('id')->where('tech_id', $product->card_id)->pluck('id')->toArray();
            foreach (json_decode($product->product) as $item) {
                $prod[] = ProductNames::select('id')->where('name', $this->getProduct_id($item->product->meta->href))->pluck('id')->toArray();
            }
        }

        for ($i=0; $i<count($id); $i++) {
            $model = new TechCardProductName();
            $model->card_id = $id[$i][0];
            $model->product_id = $prod[$i][0];
            $model->save();
        }


//        for ($i=0; $i<count($id); $i++) {
//            $model{$i} = new TechCardProductName();
//            $model{$i}->card_id = TechnicalCards::select('id')->where('tech_id', $id[$i])->get();
//            $model{$i}->product_id = ProductNames::select('id')->where('name', $this->getProduct_id($prod[$i]))->get()[0]->id;
//            $model{$i}->save();
//        }
    }
}
