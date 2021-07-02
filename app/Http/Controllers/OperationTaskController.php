<?php

namespace App\Http\Controllers;

use App\Models\Materials;
use App\Models\Products;
use App\Models\TechnicalCards;
use Carbon\Carbon;
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

    protected function getProductForDefects($card_id, $defects): array
    {
        $product = Products::select('product')->where('card_id', $card_id)->get();

        $result = [];

        foreach (json_decode($product[0]->product) as $item) {

            $result[] = [
                "quantity" => $defects,
                "price" => 0,
                "assortment" => $item->product
            ];
        }

        return $result;

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

    protected function getCardForStore($card_id)
    {
        // вытаскиваем карты со склада Берзарина
        $arrCards = TechnicalCards::select('tech_id')->where('name', 'like', '%Hybrid%')
            ->orWhere('name', 'like', '%DUO%')->get();

        $result = [];
        foreach ($arrCards as $card) {
            if ($card->tech_id === $card_id) {
                $result[]=$card->tech_id;
            }
        }

        return $result;
    }

    protected function productsStore($card_id)
    {
        $result = $this->getCardForStore($card_id);
        $store = [];

        if(count($result)) {
              $store['meta'] = [
                 "href" => "https://online.moysklad.ru/api/remap/1.1/entity/store/b437351e-8d0a-11e5-7a40-e8970059ee20",
                 "metadataHref" => "http://online.moysklad.ru/api/remap/1.1/entity/store/metadata",
                 "type" => "store",
                 "mediaType" => "application/json"
             ];
        } else {
            $store['meta'] = [
                    "href" => "https://online.moysklad.ru/api/remap/1.1/entity/store/0e054440-b971-11eb-0a80-0898000fbaed",
                    "metadataHref" => "http://online.moysklad.ru/api/remap/1.1/entity/store/metadata",
                    "type" => "store",
                    "mediaType" => "application/json"
                ];
        }
        return $store;
    }

    protected function materialsStore($card_id)
    {
        $result = $this->getCardForStore($card_id);
        $store = [];

        if(count($result)) {
            $store['meta'] = [
                 "href" => "https://online.moysklad.ru/api/remap/1.1/entity/store/b437351e-8d0a-11e5-7a40-e8970059ee20",
                 "metadataHref" => "http://online.moysklad.ru/api/remap/1.1/entity/store/metadata",
                 "type" => "store",
                 "mediaType" => "application/json"
            ];
        } else {
            $store['meta'] = [
                "href" => "https://online.moysklad.ru/api/remap/1.1/entity/store/0e054440-b971-11eb-0a80-0898000fbaed",
                "metadataHref" => "http://online.moysklad.ru/api/remap/1.1/entity/store/metadata",
                "type" => "store",
                "mediaType" => "application/json"
            ];
        }
        return $store;
    }

    protected function getMaterials()
    {
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
        date_default_timezone_set('Europe/Moscow');
        $json = [
            "organization" => [
                "meta" => [
                    "href" => "http://online.moysklad.ru/api/remap/1.1/entity/organization/75311b0b-af1f-11e7-7a6c-d2a9000300d1",
                    "metadataHref" => "http://online.moysklad.ru/api/remap/1.1/entity/organization/metadata",
                    "type" => "organization",
                    "mediaType" => "application/json"
                ]
            ],
            "processingSum" => 0,
            "quantity" => $request->count,
            "moment" => Carbon::now()->subMinute(1)->format('Y-m-d H:i:s'),
            "processingPlan" => [
                "meta" => [
                    "href" => "http://online.moysklad.ru/api/remap/1.1/entity/processingplan/" . $request->card_id,
                    "metadataHref" => "http://online.moysklad.ru/api/remap/1.1/entity/processingplan/metadata",
                    "type" => "processingplan",
                    "mediaType" => "application/json"
                ]
            ],
            "productsStore" => $this->productsStore($request->card_id),
            "materialsStore" =>$this->materialsStore($request->card_id),
            "products" => [
                "meta" => $this->getProductMeta($request->card_id),
                "rows" => $this->getProductsCard($request->card_id)
            ],
            "materials" => [
                "meta" => $this->getMaterialsMeta($request->card_id),
                "rows" => $this->getMaterialsCard($request->card_id)
            ],
        ];


        if ($request->count) {
            $response = Http::withBasicAuth('multishop@4wimax', '3hQ&ue1x')->post('https://online.moysklad.ru/api/remap/1.1/entity/processing', $json);
        }

        $defectJson = [
                "organization" => [
                    "meta" => [
                        "href" => "http://online.moysklad.ru/api/remap/1.1/entity/organization/75311b0b-af1f-11e7-7a6c-d2a9000300d1",
                        "metadataHref" => "http://online.moysklad.ru/api/remap/1.1/entity/organization/metadata",
                        "type" => "organization",
                        "mediaType" => "application/json"
                    ]
                ],
                "agent" => [
                    "meta" => [
                        "href" => "https://online.moysklad.ru/api/remap/1.1/entity/counterparty/a09246c4-da3d-11eb-0a80-0dc20018cc35",
                        "metadataHref" => "https://online.moysklad.ru/api/remap/1.1/entity/counterparty/metadata",
                        "type" => "counterparty",
                        "mediaType" => "application/json",
                    ]
                ],
                "store" => [
                    "meta" => [
                        "href"=> "https://online.moysklad.ru/api/remap/1.1/entity/store/0e054440-b971-11eb-0a80-0898000fbaed",
                        "type" => "store",
                        "mediaType" => "application/json"
                    ]
                ],
                "positions" => $this->getProductForDefects($request->card_id, $request->defects)
            ];

        if ($request->defects) {
            $response = Http::withBasicAuth('multishop@4wimax', '3hQ&ue1x')->post('https://online.moysklad.ru/api/remap/1.1/entity/demand', $defectJson);
        }

        return $response->body();
    }
}
