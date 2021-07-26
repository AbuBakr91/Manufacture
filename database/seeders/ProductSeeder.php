<?php

namespace Database\Seeders;

use App\Models\Products;
use App\Models\TechnicalCards;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class ProductSeeder extends Seeder
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
            $results[] = json_decode(Http::withBasicAuth(env('M_LOGIN'), env('M_PASS'))->get('https://online.moysklad.ru/api/remap/1.2/entity/processingplan/'.$result[$i]->tech_id.'/products'));
        }

        $output = [];

        for ($i=0;$i<count($results); $i++) {
            $meta[] = $results[$i]->meta;
            $rows[] = $results[$i]->rows;
        }

        for ($i=0;$i<count($results); $i++) {
            $model{$i} = new Products();
            $model{$i}->card_id = $result[$i]->tech_id;
            $model{$i}->meta = json_encode($meta[$i]);
            $model{$i}->product = json_encode($rows[$i]);
            $model{$i}->save();
        }
    }
}
