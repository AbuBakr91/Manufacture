<?php

namespace App\Http\Controllers;

use App\Models\Materials;
use App\Models\Products;
use App\Models\TechnicalCards;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OperationTaskController extends Controller
{
    public function spendOperation(Request $request)
    {
        $response = Http::withBasicAuth('multishop@4wimax', '3hQ&ue1x')->get('https://online.moysklad.ru/api/remap/1.2/entity/processingplan');
    }

    /**
     * @param $card_id
     * @return mixed
     */
    protected function getProductMeta($card_id)
    {
        $productData = Products::select('meta')->where('card_id', $card_id)->get();

        return json_decode($productData[0]->meta);
    }

    /**
     * @param $card_id
     * @return mixed
     */
    protected function getMaterialsMeta($card_id)
    {
        $materialsData = Materials::select('meta')->where('card_id', $card_id)->get();

        return json_decode($materialsData[0]->meta);
    }

    protected function getProductsCard($card_id)
    {
        $productData = Products::select('product')->where('card_id', $card_id)->get();

        $result = [];

        foreach (json_decode($productData[0]->product) as $item) {

            $result[] = [
                "quantity" => $item->quantity,
                "assortment" => $item->product
            ];
        }

        return $result;
    }

    protected function getMaterialsCard($card_id)
    {
        $materialsData = Materials::select('materials')->where('card_id', $card_id)->get();

        $result = [];

        foreach (json_decode($materialsData[0]->materials) as $item) {
            $result[] = [
                    "quantity" => $item->quantity,
                    "assortment" => $item->product
                ];
        }

        return $result;
    }

    protected function getMaterials()
    {
        $count = 1;
        $card_id = '00283c7f-c21d-11eb-0a80-058900230736';
        $materialData = Materials::select('meta', 'materials')->where('card_id', $card_id)->get();
        $productData = Products::select('meta', 'product')->where('card_id', $card_id)->get();

        $output = [];

        $material = json_decode($materialData[0]->materials);
        $product = json_decode($materialData[0]->product);

        foreach ($material as $item) {
            $product[] = $item->product->meta;
            $quantity[] = $item->quantity;
        }

        $output['prod'] = $product;
        $output['count'] = $quantity;

        return view('welcome', compact('output'));
    }

    public function operationMoySklad(Request $request)
    {
        $json = [
            "organization" => [
                "meta" => [
                    "href" => "http://online.moysklad.ru/api/remap/1.1/entity/organization/75311b0b-af1f-11e7-7a6c-d2a9000300d1",
                    "metadataHref" => "http://online.moysklad.ru/api/remap/1.1/entity/organization/metadata",
                    "type" => "organization",
                    "mediaType" => "application/json"
                ]
            ],
            "processingSum" => 1000,
            "quantity" => $request->count,
            "processingPlan" => [
                "meta" => [
                    "href"=> "http://online.moysklad.ru/api/remap/1.1/entity/processingplan/" . $request->card_id,
                    "metadataHref"=> "http://online.moysklad.ru/api/remap/1.1/entity/processingplan/metadata",
                    "type" => "processingplan",
                    "mediaType" => "application/json"
                ]
            ],
            "productsStore" => [
                "meta" => [
                    "href"=> "https://online.moysklad.ru/api/remap/1.1/entity/store/0e054440-b971-11eb-0a80-0898000fbaed",
                    "metadataHref"=> "http://online.moysklad.ru/api/remap/1.1/entity/store/metadata",
                    "type" => "store",
                    "mediaType" => "application/json"
                ]
            ],
            "materialsStore" => [
                "meta" => [
                    "href"=> "https://online.moysklad.ru/api/remap/1.1/entity/store/0e054440-b971-11eb-0a80-0898000fbaed",
                    "metadataHref"=> "http://online.moysklad.ru/api/remap/1.1/entity/store/metadata",
                    "type" => "store",
                    "mediaType" => "application/json"
                ]
            ],
            "products" => [
                "meta" => $this->getProductMeta($request->card_id),
                "rows" => $this->getProductsCard($request->card_id)
            ],
            "materials" => [
                "meta" => $this->getMaterialsMeta($request->card_id),
                "rows" => $this->getMaterialsCard($request->card_id)
            ],
        ];


        $data = response()->json($json);

        $response = Http::withBasicAuth('multishop@4wimax', '3hQ&ue1x')->post('https://online.moysklad.ru/api/remap/1.1/entity/processing', $json);

        return $response;
    }
}
