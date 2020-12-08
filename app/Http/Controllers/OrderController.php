<?php

namespace App\Http\Controllers;

use App\Order;
use App\OrderDetail;
use App\Service\AlertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        DB::beginTransaction();

        $order = new Order($request->all());
        $order->save();

        foreach ($request->order_details as $detail) {
            $detail = new OrderDetail($detail);
            $detail->order_id = $order->id;
            $detail->save();
        }

        DB::commit();

        AlertService::alertSuccess(__('alert.processSuccessfully'));

        return response()->json(['success' => true, 'redirect' => route('order.labels', [$order->uuid])]);
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
        $order = Order::query()->uuid($id)->with(['customer', 'courier', 'orderDetails'])->firstOrFail();

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
        $order = Order::query()->uuid($id)->firstOrFail();
        $order->status = Order::STATUS_SENT;

        if ($request->photo && $request->sign) {

            $order->photo = $order->attachDocument($request->photo, 'photo');
            $order->sign = $order->photo ? $order->attachDocument($request->sign, 'sign') : false;

            if (! $order->sign || ! $order->photo) {
                AlertService::alertFail(__('alert.invalidImageFormat'));

                return response()->json(['success' => false], 400);
            }
        }

        $order->save();
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
     * Packing list
     *
     * @return \Illuminate\Http\Response
     */
    public function packingList()
    {
        return view('order.packingList');
    }

    /**
     * Packing list process
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function packingListProcess(Request $request)
    {
        $orders = Order::query()->packingList($request->courier_id, $request->customer_id)->get();

        return response()->json(['success' => true, 'data' => $orders]);
    }
}
