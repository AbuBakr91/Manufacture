<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\MaterialForCard;
use App\Models\Materials;
use App\Models\ProductNames;
use App\Models\Products;
use App\Models\TechnicalCards;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;


class WebHooksController extends Controller
{

    protected function getMaterialsName($materialsRows, $allProducts): array
    {
        $materialsHref = [];
        foreach ($materialsRows as $key=> $row) {
            $arr = explode('/', $row->product->meta->href);
            $materialsHref[$key]['id'] = end($arr);
            $materialsHref[$key]['count'] = $row->quantity;
        }

        $name = [];
        $quantity = [];

        for($i=0; $i<count($allProducts[0]); $i++) {
            for ($j=0;$j<count($materialsHref); $j++) {
                if ($allProducts[0][$i]->id === $materialsHref[$j]['id']) {
                    $name[] = $allProducts[0][$i]->name;
                    $quantity[] = $materialsHref[$j]['count'];
                }
            }
        }

        $materialsName = [];
        for ($i=0; $i<count($name); $i++) {
            $materialsName[$i]['name'] = $name[$i];
            $materialsName[$i]['quantity'] = $quantity[$i];
        }

        return $materialsName;
    }

    public function getMaterial($card_id)
    {
        return json_decode(Http::withBasicAuth(env('M_LOGIN'), env('M_PASS'))->get('https://online.moysklad.ru/api/remap/1.1/entity/processingplan/'. $card_id .'/materials'));
    }

    public function getProducts($card_id)
    {
        return json_decode(Http::withBasicAuth(env('M_LOGIN'), env('M_PASS'))->get('https://online.moysklad.ru/api/remap/1.1/entity/processingplan/'. $card_id .'/products'));
    }

    public function recordMaterialsName($materialsData, $cardId, $allProducts)
    {
        date_default_timezone_set('Europe/Moscow');

        $materialNames = $this->getMaterialsName($materialsData->rows, $allProducts);

        for ($i=0; $i<count($cardId); $i++) {
            for ($j=0; $j<count($materialNames); $j++) {
                $model{$j} = new MaterialForCard();
                $model{$j}->card_id = $cardId[$i]->id;
                $model{$j}->product_id = ProductNames::select('id')->where('name', $materialNames[$j]['name'])->get()[0]->id;
                $model{$j}->count = $materialNames[$j]['quantity'];
                $model{$j}->save();
            }
        }
    }


    protected function createTechCard($card_id, $allProducts)
    {
        date_default_timezone_set('Europe/Moscow');

        $response = Http::withBasicAuth(env('M_LOGIN'), env('M_PASS'))->get('https://online.moysklad.ru/api/remap/1.2/entity/processingplan/' . $card_id);
        $category = $response->json('pathName');

        $categoryId = DB::table('categories')
            ->select('id')
            ->where('name', $category)->get()[0]->id;

        $tech_card = new TechnicalCards;
        $tech_card->tech_id = $card_id;
        $tech_card->name = $response->json('name');
        $tech_card->cat_id = $categoryId;
        $tech_card->save();

        $materialsData = $this->getMaterial($card_id);
        $productsData = $this->getProducts($card_id);

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
        $id = TechnicalCards::select('id')->where('tech_id', $card_id)->get();

        $this->recordMaterialsName($materialsData, $id, $allProducts);

    }

    protected function updateJsonProducts($card_id)
    {
        $products = Products::where('card_id', $card_id);
        $products->update(['product' => $this->getProducts($card_id)->rows]);
    }

    protected function updateJsonMaterials($card_id)
    {
        $materials = Materials::where('card_id', $card_id);
        $materials->update(['materials' => $this->getMaterial($card_id)->rows]);
    }

    public function updateTechCard($card_id, $allProducts)
    {
        $products = Products::where('card_id', $card_id);
        $products->update(['product' => $this->getProducts($card_id)->rows]);

        $materialsData = $this->getMaterial($card_id);
        $materials = Materials::where('card_id', $card_id);

        $materials->update(['materials' => $materialsData->rows]);

        $cardId = TechnicalCards::select('id')->where('tech_id', $card_id)->get();
        $delete = MaterialForCard::where('card_id', $cardId[0]->id);
        $delete->delete();

        $this->recordMaterialsName($materialsData, $cardId, $allProducts);
    }

    public function updateCardForId($card_id) {
        $allProduct1 = json_decode(Http::withBasicAuth(env('M_LOGIN'), env('M_PASS'))->get('https://online.moysklad.ru/api/remap/1.2/entity/product'))->rows;
        $allProduct2 = json_decode(Http::withBasicAuth(env('M_LOGIN'), env('M_PASS'))->get('https://online.moysklad.ru/api/remap/1.2/entity/product?offset=1000'))->rows;
        $allProducts[] = array_merge($allProduct1, $allProduct2);
        $this->updateTechCard($card_id, $allProducts);
    }

    protected function delete($card_id)
    {
        $techCard = TechnicalCards::where('tech_id', $card_id);
        $techCard->update([ "deleted" => true]);
    }

    public function webHook(Request $request)
    {
        $arr = explode('/', json_encode($request->events[0]));
        $arrEnd = end($arr);

        $delete  = strripos(json_encode($request->events[0]), 'DELETE');
        $create  = strripos(json_encode($request->events[0]), 'CREATE');
        $update  = strripos(json_encode($request->events[0]), 'UPDATE');

        $card = explode( '"', $arrEnd);
        $card_id = $card[0];

        if ($create) {
            $allProduct1 = json_decode(Http::withBasicAuth(env('M_LOGIN'), env('M_PASS'))->get('https://online.moysklad.ru/api/remap/1.2/entity/product'))->rows;
            $allProduct2 = json_decode(Http::withBasicAuth(env('M_LOGIN'), env('M_PASS'))->get('https://online.moysklad.ru/api/remap/1.2/entity/product?offset=1000'))->rows;
            $allProducts[] = array_merge($allProduct1, $allProduct2);
            $this->createTechCard($card_id, $allProducts);
            DB::table('cache')
                ->insert([
                    "webhook" => 'create: ' . $card_id
                ]);
        }

        if ($update) {
            $allProduct1 = json_decode(Http::withBasicAuth(env('M_LOGIN'), env('M_PASS'))->get('https://online.moysklad.ru/api/remap/1.2/entity/product'))->rows;
            $allProduct2 = json_decode(Http::withBasicAuth(env('M_LOGIN'), env('M_PASS'))->get('https://online.moysklad.ru/api/remap/1.2/entity/product?offset=1000'))->rows;
            $allProducts[] = array_merge($allProduct1, $allProduct2);
            $this->updateTechCard($card_id, $allProducts);
        }

        return response()->json([
            "status" => true
        ]);
    }
}
