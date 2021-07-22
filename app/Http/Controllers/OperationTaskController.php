<?php

namespace App\Http\Controllers;

use App\Models\MaterialForCard;
use App\Models\Materials;
use App\Models\Products;
use App\Models\TechnicalCards;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Database\Seeders\TechnicalCardAndCategoriesSeeder;

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

    /**
     * @param $card_id
     * @return array
     *
     * выводит склад в зависимости от тех карты для материалов
     */
    protected function materialsStore($card_id)
    {
        //получаем результат сравнения карт
        $result = $this->getCardForStore($card_id);

        $store = [];

        //если есть совпадение выводим склад с Берзарина
        if(count($result)) {
            $store['meta'] = [
                 "href" => "https://online.moysklad.ru/api/remap/1.1/entity/store/b437351e-8d0a-11e5-7a40-e8970059ee20",
                 "metadataHref" => "http://online.moysklad.ru/api/remap/1.1/entity/store/metadata",
                 "type" => "store",
                 "mediaType" => "application/json"
            ];
        } else {
            //слад производство 215
            $store['meta'] = [
                "href" => "https://online.moysklad.ru/api/remap/1.1/entity/store/0e054440-b971-11eb-0a80-0898000fbaed",
                "metadataHref" => "http://online.moysklad.ru/api/remap/1.1/entity/store/metadata",
                "type" => "store",
                "mediaType" => "application/json"
            ];
        }
        return $store;
    }



    protected function getCardsId(): array
    {
        $technicalCard = [];
        $response = Http::withBasicAuth('multishop@4wimax', '3hQ&ue1x')->get('https://online.moysklad.ru/api/remap/1.2/entity/processingplan');

        for ($i=0; $i<count($response['rows']); $i++) {
            $technicalCard[] = $response['rows'][$i]['id'];
        }

        return $technicalCard;
    }

    protected function getMaterialsName($card_id, $allProducts)
    {
        $materialsRows = json_decode(Materials::select('materials')->where('card_id', $card_id)->get()[0]->materials);
        $materialsHref = [];
        foreach ($materialsRows as $key=> $row) {
            $arr = explode('/', $row->product->meta->href);
            $materialsHref[$key]['id'] = end($arr);
            $materialsHref[$key]['count'] = $row->quantity;
        }

//        $allProducts = [];
//        $allProducts[] = json_decode(Http::withBasicAuth('multishop@4wimax', '3hQ&ue1x')->get('https://online.moysklad.ru/api/remap/1.2/entity/product'))->rows;
        $materialsName = [];

        for($i=0; $i<count($allProducts[0]); $i++) {
            for ($j=0;$j<count($materialsHref); $j++) {
                if ($allProducts[0][$i]->id === $materialsHref[$j]['id']) {
                    $name[] = $allProducts[0][$i]->name;
                    $quantity[] = $materialsHref[$j]['count'];
                }
            }
        }

        for ($i=0; $i<count($name); $i++) {
            $materialsName[$i]['name'] = $name[$i];
            $materialsName[$i]['quantity'] = $quantity[$i];
        }

        return $materialsName;
    }

    //для теста вывод в шаблон
    public function getMaterials()
    {
//        $technicalCard = $this->getCardsId();
//        for ($i=0; $i<count($technicalCard); $i++){
//            $output[] = TechnicalCards::where('tech_id', $technicalCard[$i])->get();
//        }


        $allProducts[] = json_decode(Http::withBasicAuth('multishop@4wimax', '3hQ&ue1x')->get('https://online.moysklad.ru/api/remap/1.2/entity/product'))->rows;


        $output[] = $this->getMaterialsName('bd0ff34e-bfcf-11eb-0a80-041d000004f5', $allProducts);

//        $technicalCard = TechnicalCards::all('tech_id');
//        $output = [];
//        foreach ($technicalCard as $card_id) {
//            $output[] = $card_id->tech_id;
//        }
        return view('welcome', compact('output'));
    }

    /**
     * @param Request $request
     * @return array
     * создаем тех операцию и отгрузку на Брак
     */
    public function operationMoySklad(Request $request)
    {
        $count = $request->defects ? $request->count + $request->defects : $request->count;
        //чтобы сохранял по мск времени
        date_default_timezone_set('Europe/Moscow');

        //формируем json для отправки в теле запроса на мойсклад
        $json = [
            "organization" => [
                "meta" => [
                    "href" => "http://online.moysklad.ru/api/remap/1.2/entity/organization/75311b0b-af1f-11e7-7a6c-d2a9000300d1",
                    "metadataHref" => "http://online.moysklad.ru/api/remap/1.2/entity/organization/metadata",
                    "type" => "organization",
                    "mediaType" => "application/json"
                ]
            ],
            "processingSum" => 0,
            "applicable" => $request->applicable ? false : true,
            "quantity" => $count,
            "moment" => $request->moment,
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
                "rows" => $this->getProductsCard($request->card_id, $count)
            ],
            "materials" => [
                "meta" => $this->getMaterialsMeta($request->card_id),
                "rows" => $this->getMaterialsCard($request->card_id, $count)
            ],
        ];


        //если передано количество создаем тех операцию
        if ($request->count) {
            $response = Http::withBasicAuth('multishop@4wimax', '3hQ&ue1x')->post('https://online.moysklad.ru/api/remap/1.1/entity/processing', $json);
        }

        //возвращаем ответ с мойсклад
        return $response->body();
    }


    public function operationDefects(Request $request)
    {
        //формируем json если переданы браки
        $defectJson = [
            "organization" => [
                "meta" => [
                    "href" => "http://online.moysklad.ru/api/remap/1.2/entity/organization/75311b0b-af1f-11e7-7a6c-d2a9000300d1",
                    "metadataHref" => "http://online.moysklad.ru/api/remap/1.2/entity/organization/metadata",
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

        //если передано количество брака создаем отгрузку на Брак
        $response = Http::withBasicAuth('multishop@4wimax', '3hQ&ue1x')->post('https://online.moysklad.ru/api/remap/1.1/entity/demand', $defectJson);

        return $response->body();
    }
}
