<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\TechnicalCards;
use App\Models\Categories;

class TechnicalCardSeeder extends Seeder
{

    /**
     * @return array
     */
    protected function getDataApi(): array
    {
        $response = Http::withBasicAuth('multishop@4wimax', '3hQ&ue1x')->get('https://online.moysklad.ru/api/remap/1.2/entity/processingplan');
        $data = $response->json();
        $technicalCard = [];

        for ($i=0; $i<count($data['rows']); $i++) {
            $technicalCard[] = $data['rows'][$i];
        }

        return $technicalCard;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $technicalCard = $this->getDataApi();

        for ($i=0; $i<count($technicalCard); $i++) {
            if (!$technicalCard[$i]['archived']) {
                $model{$i} = new TechnicalCards();
                $model{$i}->name = $technicalCard[$i]['name'];
                $model{$i}->cat_id = Categories::where('name', $technicalCard[$i]['pathName'])->get()[0]->id;
                $model{$i}->save();
            }
        }
    }
}
