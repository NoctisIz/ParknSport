<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EquipementsSportifs;
use Illuminate\Support\Facades\Http;

class test extends Controller
{
    public function index()
    {
        $response = Http::get(env('API_ANGERS'));
        
        if ($response->successful()) {
            $data = $response->json()['records'];
            return view('welcome', ['equipments' => $data]);
        }
        
        return response('Failed to fetch data from API', 500);
    }
}
