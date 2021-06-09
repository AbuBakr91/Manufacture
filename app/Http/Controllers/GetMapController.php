<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Categories;

class GetMapController extends Controller
{
    public function sortKeyArray($arr): array
    {
        $count = count($arr);
        $output = [];
        for ($i=0; $i<$count; $i++) {
            $output[$i] = array_shift($arr);
        }

        return $output;
    }

    public function index()
    {
        $response = Http::withBasicAuth('multishop@4wimax', '3hQ&ue1x')->get('https://online.moysklad.ru/api/remap/1.2/entity/processingplan');
        $data = $response->json();
        $categories = [];

        for ($i=0; $i<count($data['rows']); $i++) {
            $categories[] = $data['rows'][$i];
        }

        return view('welcome', compact('cat_id'));
    }
}
