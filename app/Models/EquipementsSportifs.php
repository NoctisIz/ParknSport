<?php

namespace App\Models;

use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;

class EquipementsSportifs extends Model
{
    protected $baseUrl = 'https://angersloiremetropole.opendatasoft.com/api/explore/v2.1/catalog/datasets/equipements-sportifs-angers/records?limit=20';

    public function getAllEquipments()
    {
        $response = Http::get("{$this->baseUrl}/endpoint");
        return $response->successful() ? $response->json() : [];
    }

    public function findEquipements($id)
    {
        $response = Http::get("{$this->baseUrl}/endpoint/{$id}");
        return $response->successful() ? $response->json() : null;
    }
}
