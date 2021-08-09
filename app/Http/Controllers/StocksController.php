<?php

namespace App\Http\Controllers;

use App\Models\MaterialForCard;
use App\Models\StockModel;
use App\Models\TechCardProductName;
use App\Models\TechnicalCards;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class StocksController extends Controller
{
    public function index()
    {
        $products = DB::table('stock_models as sm')
            ->leftJoin('product_names as pn', 'pn.id', '=', 'sm.product_name_id')
            ->select('pn.name', 'sm.*')
            ->whereNotIn('sm.product_name_id', MaterialForCard::select('product_id')->pluck('product_id')->toArray())
            ->get();


        $arr = [];
        foreach ($products as $product) {
            $arr[] = $this->getCardsForProduct($product->product_name_id);
        }

        return $arr;
    }

    public function getCardsForProduct($product_id)
    {
        return TechCardProductName::where('product_id', $product_id)->pluck('card_id')->toArray();
    }

    /**
     * @param $products
     * @return array
     */
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

    public function editSalesRatio(Request $request, $id)
    {
        try {
            $salesRatio = StockModel::where('id', $id);
            $salesRatio->update(["salesRatio" => $request->ratio]);

            return response()->json(["status" => 200]);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

}
