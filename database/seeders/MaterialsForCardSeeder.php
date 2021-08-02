<?php

namespace Database\Seeders;

use App\Models\Categories;
use App\Models\ProductNames;
use App\Models\TechnicalCards;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\MaterialForCard;

class MaterialsForCardSeeder extends Seeder
{

    protected function getCardsId(): array
    {
        $technicalCard = [];
        $response = Http::withBasicAuth(env('M_LOGIN'), env('M_PASS'))->get('https://online.moysklad.ru/api/remap/1.2/entity/processingplan');

        for ($i=0; $i<count($response['rows']); $i++) {
            $technicalCard[] = $response['rows'][$i]['id'];
        }

        return $technicalCard;
    }

    protected function getMaterialsName($card_id): array
    {
        $response = Http::withBasicAuth(env('M_LOGIN'), env('M_PASS'))->get('https://online.moysklad.ru/api/remap/1.2/entity/processingplan/' . $card_id . '/materials');

        foreach (json_decode($response)->rows as $row) {
            $materialsHref[] = $row->product->meta->href;
        }

        $materialsName = [];

        foreach ($materialsHref as $item) {
            $materialsName[] = json_decode(Http::withBasicAuth(env('M_LOGIN'), env('M_PASS'))->get($item))->name;
        }

        return $materialsName;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $allProduct1 = json_decode(Http::withBasicAuth(env('M_LOGIN'), env('M_PASS'))->get('https://online.moysklad.ru/api/remap/1.2/entity/product'))->rows;
        $allProduct2 = json_decode(Http::withBasicAuth(env('M_LOGIN'), env('M_PASS'))->get('https://online.moysklad.ru/api/remap/1.2/entity/product?offset=1000'))->rows;
        $allProducts[] = array_merge($allProduct1, $allProduct2);

        for ($i=0; $i<count($allProducts[0]); $i++) {
                $model{$i} = new ProductNames();
                $model{$i}->name = $allProducts[0][$i]->name;;
                $model{$i}->save();
        }
    }
}
