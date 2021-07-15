<?php

namespace App\Http\Controllers;

use App\Models\Materials;
use App\Models\Products;
use App\Models\TechnicalCards;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class WebHooksController extends Controller
{
    protected function create($card_id)
    {
        $response = Http::withBasicAuth('multishop@4wimax', '3hQ&ue1x')->get('https://online.moysklad.ru/api/remap/1.2/entity/processingplan/' . $card_id);
        $category = $response->json('pathName');

        $categoryId = CategoryController::where('name', $category);

        $tech_card = new TechnicalCards;
        $tech_card->tech_id = $card_id;
        $tech_card->name = $response->json('name');
        $tech_card->cat_id = $categoryId;
        $tech_card->save();
    }

    protected function update($card_id)
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
        if ($request->events[0]->action === 'CREATE') {
            $this->create($request->events[0]->id);
        }

        if ($request->events[0]->action === 'UPDATE') {
            $this->update($request->events[0]->id);
        }
    }
}
