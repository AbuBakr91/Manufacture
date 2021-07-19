<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Materials;
use App\Models\Products;
use App\Models\TechnicalCards;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;


class WebHooksController extends Controller
{
    protected function createTechCard($card_id)
    {
        $response = Http::withBasicAuth('multishop@4wimax', '3hQ&ue1x')->get('https://online.moysklad.ru/api/remap/1.2/entity/processingplan/' . $card_id);
        $category = $response->json('pathName');

        $categoryId = DB::table('categories')
            ->select('id')
            ->where('name', $category)->get()[0]->id;

        $tech_card = new TechnicalCards;
        $tech_card->tech_id = $card_id;
        $tech_card->name = $response->json('name');
        $tech_card->cat_id = $categoryId;
        $tech_card->save();

        $materialsData = json_decode(Http::withBasicAuth('multishop@4wimax', '3hQ&ue1x')->get('https://online.moysklad.ru/api/remap/1.1/entity/processingplan/'. $card_id .'/materials'));
        $productsData = json_decode(Http::withBasicAuth('multishop@4wimax', '3hQ&ue1x')->get('https://online.moysklad.ru/api/remap/1.1/entity/processingplan/'. $card_id .'/products'));

        $material = new Materials();
        $material->card_id = $card_id;
        $material->meta = json_encode($materialsData->meta);
        $material->materials = json_encode($materialsData->rows);
        $material->save();

        $product = new Products();
        $product->card_id = $card_id;
        $product->meta = json_encode($productsData->meta);
        $product->product = json_encode($productsData->rows);
        $product->save();

    }

    protected function updateTechCard($card_id)
    {
        $materials = Materials::where('card_id', $card_id);
        $products = Products::where('card_id', $card_id);

        $materialsData = json_decode(Http::withBasicAuth('multishop@4wimax', '3hQ&ue1x')->get('https://online.moysklad.ru/api/remap/1.1/entity/processingplan/'. $card_id .'/materials'));
        $productsData = json_decode(Http::withBasicAuth('multishop@4wimax', '3hQ&ue1x')->get('https://online.moysklad.ru/api/remap/1.1/entity/processingplan/'. $card_id .'/products'));

        $materialsRows = $materialsData->rows;
        $productsRows = $productsData->rows;

        $materials->update(['materials' => $materialsRows]);
        $products->update(['product' => $productsRows]);
    }

    protected function delete($card_id)
    {

    }

    public function webHook(Request $request)
    {
        $arr = explode('/', json_encode($request->events[0]));
        $arrEnd = end($arr);

        $delete  = strripos(json_encode($request->events[0]), 'DELETE');
        $create  = strripos(json_encode($request->events[0]), 'CREATE');
        $update  = strripos(json_encode($request->events[0]), 'UPDATE');

        $card = explode( '"', $arrEnd);
        $cardId = $card[0];



        if ($create) {
            $this->createTechCard($cardId);
        }

        if ($update) {
            $this->updateTechCard($cardId);
            DB::table('cache')
                ->insert([
                    "webhook" => $cardId
                ]);
        }

        return response()->json([
            "status" => true
        ]);
    }
}
