<?php

namespace App\Http\Controllers;

use App\Order;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $orders = Order::query()->today()->count();
        $customers = Order::query()->today()->distinct('customer_id')->count();
        $boxes = Order::query()
            ->today()
            ->join('order_details', 'order_details.order_id', '=', 'orders.id')
            ->sum('order_details.qty')
        ;

        return view('home', compact('orders', 'customers', 'boxes'));
    }
}
