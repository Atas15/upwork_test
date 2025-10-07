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
        $clients = Client::orderBy('id')
            ->get()
            ->transform(function ($obj) {
                return [
                    'id' => $obj->id,
                    'location_id' => $obj->location_id,
                    'first_name' => $obj->first_name,
                    'last_name' => $obj->last_name,
                    'avatar' => $obj->avatar,
                    'username' => $obj->username,
                ];
            });

        return response()->json([
            'status' => 1,
            'data' => $clients,
        ], Response::HTTP_OK);
    }

}
