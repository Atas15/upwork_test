<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Location;
use Symfony\Component\HttpFoundation\Response;

class ClientController extends Controller
{
    public function index()
    {

        return response()->json([
            'status' => 1,
            'data' => [
                'clients' => Client::with('location')
                    ->get(),
            ],
        ], Response::HTTP_OK);
    }

}
