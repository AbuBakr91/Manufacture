<?php

namespace Database\Seeders;

use App\Models\MaterialForCard;
use App\Models\Materials;
use App\Models\TechnicalCards;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class MaterialNameSeeder extends Seeder
{
    protected function getCards()
    {
        $output = [];
        $technicalCard = TechnicalCards::all('tech_id');
        foreach ($technicalCard as $card_id) {
            $output[] = $card_id->tech_id;
        }

        return $output;
    }

    protected function getMaterialsName($card_id, $allProducts): array
    {
        $materialsRows = json_decode(Materials::select('materials')->where('card_id', $card_id)->get()[0]->materials);
        $materialsHref = [];
        foreach ($materialsRows as $key=> $row) {
            $arr = explode('/', $row->product->meta->href);
            $materialsHref[$key]['id'] = end($arr);
            $materialsHref[$key]['count'] = $row->quantity;
        }

        $allProducts = [];
        $allProducts[] = json_decode(Http::withBasicAuth('multishop@4wimax', '3hQ&ue1x')->get('https://online.moysklad.ru/api/remap/1.2/entity/product'))->rows;
        $materialsName = [];
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

        for ($i=0; $i<count($name); $i++) {
            $materialsName[$i]['name'] = $name[$i];
            $materialsName[$i]['quantity'] = $quantity[$i];
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
        $cards = $this->getCards();
        $allProducts[] = json_decode(Http::withBasicAuth(env('M_LOGIN'), env('M_PASS'))->get('https://online.moysklad.ru/api/remap/1.2/entity/product'))->rows;

        for ($i=0; $i<count($cards); $i++) {
            for ($j=0; $j<count($this->getMaterialsName($cards[$i], $allProducts)); $j++) {
                $model{$j} = new MaterialForCard();
                $model{$j}->card_id = TechnicalCards::select('id')->where('tech_id', $cards[$i])->get()[0]->id;
                $model{$j}->material_name = $this->getMaterialsName($cards[$i], $allProducts)[$j]['name'];
                $model{$j}->count = $this->getMaterialsName($cards[$i], $allProducts)[$j]['quantity'];
                $model{$j}->save();
            }
        }
    }
}
