<?php

namespace Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\TechnicalCards;
use App\Models\Categories;

class TechnicalCardAndCategoriesSeeder extends Seeder
{

    /**
     * @return array
     */
    protected function getDataApi()
    {
        $response = Http::withBasicAuth(env('M_LOGIN'), env('M_PASS'))->get('https://online.moysklad.ru/api/remap/1.2/entity/processingplan');
        return $response->json();
    }

    protected function getCatForApi(): array
    {
        $arr = $this->getDataApi();

        $categories = [];

        for ($i=0; $i<count($arr['rows']); $i++) {
            $categories[] = $arr['rows'][$i]['pathName'];
        }

        return array_unique($categories);
    }

    public function sortKeyArray($arr): array
    {
        $count = count($arr);
        $output = [];
        for ($i=0; $i<$count; $i++) {
            $output[$i] = array_shift($arr);
        }

        return $output;
    }

    protected function category(): array
    {
        $data = $this->sortKeyArray($this->getCatForApi());
    }

    protected function technologyCard(): array
    {
        $data = $this->getDataApi();

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
        $category = $this->sortKeyArray($this->getCatForApi());

        for ($i=0; $i<count($category); $i++) {
            $model{$i} = new Categories();
            if ($category[$i] !== '') {
                $model{$i}->name = $category[$i];
                $model{$i}->save();
            }
        }

        $technicalCard = $this->technologyCard();
        for ($i=0; $i<count($technicalCard); $i++) {
            if (!$technicalCard[$i]['archived']) {
                $model{$i} = new TechnicalCards();
                $model{$i}->tech_id = $technicalCard[$i]['id'];
                $model{$i}->name = $technicalCard[$i]['name'];
                $model{$i}->cat_id = Categories::where('name', $technicalCard[$i]['pathName'])->get()[0]->id;
                $model{$i}->save();
            }
        }
    }
}
