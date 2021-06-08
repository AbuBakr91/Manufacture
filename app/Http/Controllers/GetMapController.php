<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Categories;

class GetMapController extends Controller
{
    public function index()
    {
        $response = Http::withBasicAuth('multishop@4wimax', '3hQ&ue1x')->get('https://online.moysklad.ru/api/remap/1.2/entity/processingplan');
        $data = $response->json();

        $tech_card = [];
        for ($i=0; $i<count($data['rows']); $i++) {
            if (!$data['rows'][$i]['archived']) {
                $tech_card[] = $data['rows'][$i]['pathName'];
            }

        }
        $cat_id =[];

        for ($i=0; $i<count($tech_card); $i++) {

            $cat_id[] = Categories::where('name', $tech_card[$i])->get();
        }

        return view('welcome', compact('cat_id'));
    }
}
