<?php

namespace App\Http\Controllers;

use App\Models\MaterialForCard;
use App\Models\ProductNames;
use App\Models\Products;
use App\Models\StockModel;
use App\Models\TechCardProductName;
use App\Models\TechnicalCards;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class TestController extends Controller
{

    public function getCardsForProduct($product_id)
    {
        return TechCardProductName::where('product_id', $product_id)->pluck('card_id')->toArray();
    }

    public function cardMaterials($products): array
    {
        $card = [];
        foreach ($products as $product) {
            if (count($this->getCardsForProduct($product)) !== 0) {
                $card[] = $this->getCardsForProduct($product);
            }
        }

        $materials = [];
        foreach ($card as $key => $item) {
            $materials[$key]['id'] = $item[0];
            $materials[$key]['name'] = TechnicalCards::select('name')->where('id', $item[0])->get()[0]->name;
            if (count(MaterialForCard::where('card_id', $item)->pluck('product_id')->toArray()) > 1) {
                $materials[$key]['materials'] = $this->cardMaterials(MaterialForCard::where('card_id', $item)->pluck('product_id')->toArray());
            }
        }

        return $materials;
    }

    //для теста
    public function testData()
    {
        $products = DB::table('stock_models as sm')
           ->leftJoin('product_names as pn', 'pn.id', '=', 'sm.product_name_id')
           ->select( 'sm.product_name_id')
           ->whereNotIn('sm.product_name_id', MaterialForCard::select('product_id')->pluck('product_id')->toArray())
           ->get();


        $arr = [];
        foreach ($products as $product) {
                $arr[] = $this->getCardsForProduct($product->product_name_id);
        }


//        $materials = [];
//        foreach ($arr as $key => $item) {
//            $id[] = $item[0];
//            $name[] = TechnicalCards::select('name')->where('id', $item[0])->get()[0]->name;
//            $materials[] = MaterialForCard::where('card_id', $item)->pluck('product_id')->toArray();
//        }
//
//        $output = [];
//        for ($i=0; $i<count($materials); $i++) {
//            $output[$i]['id'] = $id[$i];
//            $output[$i]['name'] = $name[$i];
//            $output[$i]['materials'] = $materials[$i];
//        }


       dd($this->cardMaterials($arr));
    }

    private function getProduct_id($href)
    {
        $response = json_decode(Http::withBasicAuth(env('M_LOGIN'), env('M_PASS'))->get("$href"));

        return $response->name;
    }

    public function migrate()
    {
        Artisan::call('db:seed --class=TechCarNamesSeeder');
    }
}
