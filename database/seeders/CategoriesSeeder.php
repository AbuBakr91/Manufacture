<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\Categories;

class CategoriesSeeder extends Seeder
{
    /**
     * @return array
     */
    protected function getDataApi(): array
    {
        $response = Http::withBasicAuth('multishop@4wimax', '3hQ&ue1x')->get('https://online.moysklad.ru/api/remap/1.2/entity/processingplan');
        $data = $response->json();
        $categories = [];

        for ($i=0; $i<count($data['rows']); $i++) {
            $categories[] = $data['rows'][$i]['pathName'];
        }

        return array_unique($categories);
    }

    /**
     * @param $arr
     * @return array
     */
    public function sortKeyArray($arr): array
    {
        $count = count($arr);
        $output = [];
        for ($i=0; $i<$count; $i++) {
            $output[$i] = array_shift($arr);
        }

        return $output;
    }


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $data = $this->getDataApi();

        $category = $this->sortKeyArray($data);


        for ($i=0; $i<count($category); $i++) {
            $model{$i} = new Categories();
            if ($category[$i] !== '') {
                $model{$i}->name = $category[$i];
                $model{$i}->save();
            }
        }

    }
}
