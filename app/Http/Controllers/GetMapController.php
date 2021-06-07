<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class GetMapController extends Controller
{
    public function index()
    {
        $response = Http::withBasicAuth('multishop@4wimax', '3hQ&ue1x')->get('https://online.moysklad.ru/api/remap/1.2/entity/processingplan');
        $data = $response->json();
        return view('welcome', compact('data'));
    }
}
