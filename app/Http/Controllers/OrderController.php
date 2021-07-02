<?php

namespace App\Http\Controllers;

use App\Mail\OrderCreated;
use App\Order;
use App\OrderDetail;
use App\Service\AlertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{

    /**
     * construct
     */
    public function __construct()
    {
        $this->middleware('admin')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $orders = Order::query()
            ->search($request->search)
            ->my()
            ->whereNull('packing_list_id')
            ->with(['customer', 'courier'])
            ->orderBy('date', 'DESC')
            ->paginate();

        return view('order.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('order.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate(['invoice_number' => 'unique:orders'], $request->all());

        DB::beginTransaction();

        $order = new Order($request->all());
        $order->save();

        foreach ($request->order_details as $detail) {

            $rate = OrderDetail::getRate($order->customer, $order->courier, $order->way);

            $detail = new OrderDetail($detail);
            $detail->order_id = $order->id;
            $detail->price = $detail->volumetricWeight() * $detail->qty * $rate;
            $detail->save();
        }

        Mail::to($order->customer->customerEmails->pluck('email')->toArray())->send(new OrderCreated($order));

        DB::commit();

        AlertService::alertSuccess(__('alert.processSuccessfully'));

        return response()->json(['success' => true, 'redirect' => route('order.edit', [$order->uuid])]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $order = Order::query()
            ->uuid($id)
            ->my()
            ->with(['customer', 'courier', 'orderDetails', 'packingList.packingListImages', 'createdBy'])
            ->firstOrFail()
        ;

        return view('order.form', compact('order'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        $order = Order::query()->uuid($id)->firstOrFail();
        $order->fill($request->only(['comment', 'courier_id', 'way']));
        $order->save();

        foreach ($order->orderDetails as $detail) {

            $rate = OrderDetail::getRate($order->customer, $order->courier, $order->way);

            $detail->price = $detail->volumetricWeight() * $detail->qty * $rate;
            $detail->save();
        }

        Mail::to($order->customer->customerEmails->pluck('email')->toArray())->send(new OrderCreated($order));

        DB::commit();

        AlertService::alertSuccess(__('alert.processSuccessfully'));

        return response(['success' => true, 'redirect' => route('order.edit', [$id])]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = Order::query()->uuid($id)->firstOrFail();
        $order->delete();

        AlertService::alertSuccess(__('alert.processSuccessfully'));

        return response()->json(['success' => true, 'redirect' => route('order.index')]);
    }

    /**
     * Report
     *
     * @return \Illuminate\Http\Response
     */
    public function report()
    {
        return view('order.report');
    }

    /**
     * Report order
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function reportProcess(Request $request)
    {
        $orders = Order::query()->report($request->all())->get();

        return response()->json(['success' => true, 'data' => $orders]);
    }

    /**
     * Labels
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function labels($id)
    {
        $order = Order::query()
            ->uuid($id)
            ->withBoxesSum()
            ->with(['customer', 'courier'])
            ->firstOrFail()
        ;

        return view('order.labels', compact('order'));
    }

    /**
     * Packing list process
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function packingListProcess(Request $request)
    {
        $orders = Order::query()->pendingForPackingList($request->all())->get();

        return response()->json(['success' => true, 'data' => $orders]);
    }
}
