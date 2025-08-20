<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Location; // << Ini yang bener, model di-import dengan use biasa

class LocationController extends Controller
{
    public function location()
    {
        $locations = Location::select('id', 'name')->get();

        return response()->json([
            'locations' => $locations
        ]);
    }
}
