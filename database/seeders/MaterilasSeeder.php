<?php

namespace Database\Seeders;

use App\Models\Materials;
use App\Models\TechnicalCards;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class MaterilasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $result = json_decode(TechnicalCards::select('tech_id')->get());

        for($i=0;$i<count($result); $i++) {
            $results[] = json_decode(Http::withBasicAuth('multishop@4wimax', '3hQ&ue1x')->get('https://online.moysklad.ru/api/remap/1.1/entity/processingplan/'.$result[$i]->tech_id.'/materials'));
        }

        $output = [];

        for ($i=0;$i<count($results); $i++) {
            $meta[] = $results[$i]->meta;
            $rows[] = $results[$i]->rows;
        }

        for ($i=0;$i<count($results); $i++) {
            $model{$i} = new Materials();
            $model{$i}->card_id = $result[$i]->tech_id;
            $model{$i}->meta = json_encode($meta[$i]);
            $model{$i}->materials = json_encode($rows[$i]);
            $model{$i}->save();
        }

    }
}
