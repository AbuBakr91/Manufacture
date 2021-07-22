<?php

namespace Database\Seeders;

use App\Models\Categories;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\MaterialForCard;

class MaterialsForCardSeeder extends Seeder
{

    protected function getCardsId(): array
    {
        $technicalCard = [];
        $response = Http::withBasicAuth('multishop@4wimax', '3hQ&ue1x')->get('https://online.moysklad.ru/api/remap/1.2/entity/processingplan');

        for ($i=0; $i<count($response['rows']); $i++) {
            $technicalCard[] = $response['rows'][$i]['id'];
        }

        return $technicalCard;
    }

    protected function getMaterialsName($card_id): array
    {
        $response = Http::withBasicAuth('multishop@4wimax', '3hQ&ue1x')->get('https://online.moysklad.ru/api/remap/1.2/entity/processingplan/' . $card_id . '/materials');

        foreach (json_decode($response)->rows as $row) {
            $materialsHref[] = $row->product->meta->href;
        }

        $materialsName = [];

        foreach ($materialsHref as $item) {
            $materialsName[] = json_decode(Http::withBasicAuth('multishop@4wimax', '3hQ&ue1x')->get($item))->name;
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
//        $technicalCard = $this->getCardsId();
        $output = [];
        $technicalCard = ['bd0ff34e-bfcf-11eb-0a80-041d000004f5', '0c5c5fe1-c6a1-11ea-0a80-03e0000aac0a'];
        foreach ($technicalCard as $card_id) {
            $output[] = $this->getMaterialsName($card_id);
        }

        for ($i=0; $i<count($technicalCard); $i++) {
            for ($j=0; $j<count($output); $j++) {
                $model{$j} = new MaterialForCard();
                $model{$j}->card_id = $technicalCard[$i];
                $model{$j}->name = $output[$j]['name'];
                $model{$i}->save();
            }
        }

        $card_id = 'bd0ff34e-bfcf-11eb-0a80-041d000004f5';
        $response = Http::withBasicAuth('multishop@4wimax', '3hQ&ue1x')->get('https://online.moysklad.ru/api/remap/1.2/entity/processingplan/' . $card_id . '/materials');

    }
}
