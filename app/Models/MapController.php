<?php

namespace App\Models;

use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class MapController extends Model
{
    public function index()
    {
        return view('map');
    }

    public function getEquipments(Request $request)
    {
        $response = Http::get('https://angersloiremetropole.opendatasoft.com/api/explore/v2.1/catalog/datasets/equipements-sportifs-angers/records', [
            'limit' => 100
        ]);

        return response()->json($response->json());
    }
}
