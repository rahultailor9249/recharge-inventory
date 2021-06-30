<?php

namespace App\Http\Controllers;

use App\Jobs\OrdersCancelJob;
use App\Jobs\OrdersCreateJob;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function add(Request $request) {
        $shop = User::where('id',1)->first();
        $response = $shop->api()->rest('POST', '/admin/api/2021-04/orders.json',$request->all());
        dd($response->body);
    }
    public function listData(Request $request) {
        $shop = User::where('id',1)->first();
        $response = $shop->api()->rest('GET','/admin/api/2021-04/orders/3858585583789.json');
        OrdersCancelJob::dispatch($shop->name,$response['body']['order']->toArray());
    }
}
