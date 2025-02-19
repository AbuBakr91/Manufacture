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
    /**
     * @param $card_id
     * @return mixed
     * формируем meta для json объекта
     */
    protected function getProductMeta($card_id)
    {
        $productData = Products::select('meta')->where('card_id', $card_id)->get();

        return json_decode($productData[0]->meta);
    }

    /**
     * @param $card_id
     * @return mixed
     * формируем meta для json объекта
     */
    protected function getMaterialsMeta($card_id)
    {
        $materialsData = Materials::select('meta')->where('card_id', $card_id)->get();

        return json_decode($materialsData[0]->meta);
    }

    /**
     * @param $card_id
     * @param $defects
     * @return array
     * вытаскиваем продукт для формирования json объекта
     */
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

    /**
     * @param $card_id
     * @param $count
     * @return array
     *
     * вытаскиваем продукты по тех карте
     */
    protected function getProductsCard($card_id, $count)
    {
        $productData = Products::select('product')->where('card_id', $card_id)->get();

        $result = [];

        foreach (json_decode($productData[0]->product) as $item) {

            $result[] = [
                "quantity" => $count,
                "assortment" => $item->product
            ];
        }

        return $result;
    }

    /**
     * @param $card_id
     * @param $count
     * @return array
     *
     * вытаскиваем материалы по тех карте
     */
    protected function getMaterialsCard($card_id, $count)
    {
        $materialsData = Materials::select('materials')->where('card_id', $card_id)->get();

        $result = [];

        foreach (json_decode($materialsData[0]->materials) as $item) {
            $result[] = [
                    "quantity" => $count,
                    "assortment" => $item->product
                ];
        }

        return $result;
    }

    /**
     * @param $card_id
     * @return array
     * ищем тех карты со склада Берзарина 12
     */
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

    /**
     * @param $card_id
     * @return array
     * выводит склад в зависимости от тех карты для материалов
     */
    protected function productsStore($card_id, $office)
    {
        $result = $this->getCardForStore($card_id);

        $store = [];

        if(count($result)) {
              $store['meta'] = [
                 "href" => "https://online.moysklad.ru/api/remap/1.2/entity/store/b437351e-8d0a-11e5-7a40-e8970059ee20",
                 "metadataHref" => "https://online.moysklad.ru/api/remap/1.2/entity/store/metadata",
                 "type" => "store",
                 "mediaType" => "application/json"
             ];
        } else {
            $store['meta'] = [
                "href" => $this->getOffice($office),
                "metadataHref" => "https://online.moysklad.ru/api/remap/1.2/entity/store/metadata",
                "type" => "store",
                "mediaType" => "application/json"
            ];
        }
        return $store;
    }

    /**
     * @param $card_id
     * @return array
     *
     * выводит склад в зависимости от тех карты для материалов
     */
    protected function materialsStore($card_id, $office)
    {
        //получаем результат сравнения карт
        $result = $this->getCardForStore($card_id);

        $store = [];

        //если есть совпадение выводим склад с Берзарина
        if(count($result)) {
            $store['meta'] = [
                 "href" => "https://online.moysklad.ru/api/remap/1.1/entity/store/b437351e-8d0a-11e5-7a40-e8970059ee20",
                 "metadataHref" => "https://online.moysklad.ru/api/remap/1.1/entity/store/metadata",
                 "type" => "store",
                 "mediaType" => "application/json"
            ];
        } else {
            $store['meta'] = [
                "href" => $this->getOffice($office),
                "metadataHref" => "https://online.moysklad.ru/api/remap/1.1/entity/store/metadata",
                "type" => "store",
                "mediaType" => "application/json"
            ];
        }
        return $store;
    }



    public function getCardsId(): array
    {
        $technicalCard = [];
        $response = Http::withBasicAuth(env('M_LOGIN'), env('M_PASS'))->get('https://online.moysklad.ru/api/remap/1.2/entity/processingplan');

        for ($i=0; $i<count($response['rows']); $i++) {
            $technicalCard[] = $response['rows'][$i]['id'];
        }

        return $technicalCard;
    }

    protected function getSellQuantity($name, $products)
    {
        foreach ($products->rows as $row) {
            if ($row->assortment->name === $name) {
                return (int)$row->sellQuantity;
            }
        }
    }

    protected function returnQuantity($name, $products)
    {
        foreach ($products->rows as $row) {
            if ($row->assortment->name === $name) {
                return (int)$row->returnQuantity;
            }
        }
    }

    /**
     * @param Request $request
     * @return string
     * создаем тех операцию и отгрузку на Брак
     */
    public function operationMoySklad(Request $request)
    {
        $count = (int)$request->defects ? (int)$request->count + (int)$request->defects : (int)$request->count;
        //чтобы сохранял по мск времени
        date_default_timezone_set('Europe/Moscow');

        //формируем json для отправки в теле запроса на мойсклад
        $json = [
            "organization" => [
                "meta" => [
                    "href" => "https://online.moysklad.ru/api/remap/1.2/entity/organization/12953484-8c3d-11e5-90a2-8ecb004019e1",
                    "metadataHref" => "http://online.moysklad.ru/api/remap/1.2/entity/organization/metadata",
                    "type" => "organization",
                    "mediaType" => "application/json"
                ]
            ],
            "processingSum" => 0,
            "moment" => Carbon::now()->subMinute()->format('Y-m-d H:i:s'),
            "applicable" => $request->applicable ? false : true,
            "quantity" => $count,
            "processingPlan" => [
                "meta" => [
                    "href" => "https://online.moysklad.ru/api/remap/1.2/entity/processingplan/" . $request->card_id,
                    "metadataHref" => "http://online.moysklad.ru/api/remap/1.2/entity/processingplan/metadata",
                    "type" => "processingplan",
                    "mediaType" => "application/json"
                ]
            ],
            "productsStore" => $this->productsStore($request->card_id, $request->office),
            "materialsStore" =>$this->materialsStore($request->card_id, $request->office),
            "products" => [
                "meta" => $this->getProductMeta($request->card_id),
                "rows" => $this->getProductsCard($request->card_id, $count)
            ],
            "materials" => [
                "meta" => $this->getMaterialsMeta($request->card_id),
                "rows" => $this->getMaterialsCard($request->card_id, $count)
            ],
        ];


        //если передано количество создаем тех операцию
        if ($request->count) {
            $response = Http::withBasicAuth(env('M_LOGIN'), env('M_PASS'))->post('https://online.moysklad.ru/api/remap/1.1/entity/processing', $json);
        }

        //возвращаем ответ с мойсклад
        return $response->body();
    }


    public function operationDefects(Request $request)
    {
        date_default_timezone_set('Europe/Moscow');

        //формируем json если переданы браки
        $defectJson = [
            "organization" => [
                "meta" => [
                    "href" => "https://online.moysklad.ru/api/remap/1.2/entity/organization/12953484-8c3d-11e5-90a2-8ecb004019e1",
                    "metadataHref" => "https://online.moysklad.ru/api/remap/1.2/entity/organization/metadata",
                    "type" => "organization",
                    "mediaType" => "application/json"
                ]
            ],
            "applicable" => $request->applicable ? false : true,
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
                    "href"=> $this->getOffice($request->office),
                    "type" => "store",
                    "mediaType" => "application/json"
                ]
            ],
            "positions" => $this->getProductForDefects($request->card_id, (int)$request->defects)
        ];

        //если передано количество брака создаем отгрузку на Брак
        $response = Http::withBasicAuth(env('M_LOGIN'), env('M_PASS'))->post('https://online.moysklad.ru/api/remap/1.1/entity/demand', $defectJson);

        return $response->body();
    }

    public function operationRetailShift(Request $request): string
    {
        date_default_timezone_set('Europe/Moscow');

        $json = [
            "sum" => 0,
            "organization" => [
                "meta" => [
                    "href" => "https://online.moysklad.ru/api/remap/1.2/entity/organization/12953484-8c3d-11e5-90a2-8ecb004019e1",
                    "metadataHref" => "https://online.moysklad.ru/api/remap/1.2/entity/organization/metadata",
                    "type" => "organization",
                    "mediaType" => "application/json"
                ]
            ],
            "applicable" => $request->applicable ? false : true,
            "store" => [
                "meta" => [
                    "href" => "https://online.moysklad.ru/api/remap/1.2/entity/store/eadaa0c3-ef77-11eb-0a80-06a800064572",
                    "metadataHref" => "https://online.moysklad.ru/api/remap/1.2/entity/store/metadata",
                    "type" => "store",
                    "mediaType" => "application/json"
                ]
            ],
            "positions" => $this->getProductForDefects($request->card_id, (int)$request->defects)
        ];

        $response = Http::withBasicAuth(env('M_LOGIN'), env('M_PASS'))->post('https://online.moysklad.ru/api/remap/1.1/entity/enter', $json);

        return $response->body();
    }

    private function getOffice($office)
    {
        if($office === '215 офис') {
            return "https://online.moysklad.ru/api/remap/1.2/entity/store/0e054440-b971-11eb-0a80-0898000fbaed";
        }

        if ($office === '220 офис') {
            return "https://online.moysklad.ru/api/remap/1.2/entity/store/14ba2afe-b971-11eb-0a80-0582000f9d1c";
        }

        if ($office === 'Склад') {
            return "https://online.moysklad.ru/api/remap/1.2/entity/store/b437351e-8d0a-11e5-7a40-e8970059ee20";
        }

        return false;
    }
}
