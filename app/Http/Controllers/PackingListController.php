<?php

namespace App\Http\Controllers;

use App\Order;
use App\PackingList;
use App\PackingListImage;
use App\Service\AlertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PackingListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $packingLists = PackingList::query()
            ->search($request->search)
            ->with(['courier'])
            ->orderBy('created_at', 'DESC')
            ->paginate();

        return view('packing-list.index', compact('packingLists'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('packing-list.form');
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

        $packingList = new PackingList($request->all());
        $packingList->save();

        Order::query()->whereIn('id', $request->orderIds)->update([
            'packing_list_id' => $packingList->id
        ]);

        DB::commit();

        AlertService::alertSuccess(__('alert.processSuccessfully'));

        return response()->json(['success' => true, 'redirect' => route('packing-list.edit', $packingList->uuid)]);
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
        $packingList = PackingList::query()
            ->uuid($id)
            ->with([
                'courier',
                'orders.orderDetails',
                'orders.customer',
                'packingListImages'
            ])
            ->firstOrFail()
        ;

        return view('packing-list.form', compact('packingList'));
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

        $packingList = PackingList::query()->uuid($id)->firstOrFail();
        $packingList->status = PackingList::STATUS_SENT;
        $packingList->sign = $packingList->attachDocument($request->sign, 'sign');

        if (! $packingList->sign) {
            AlertService::alertFail(__('alert.invalidImageFormat'));

            return response()->json(['success' => false], 400);
        }

        $packingList->save();

        foreach ($request->packing_list_images as $i => $image) {
            $packingListImage = new PackingListImage();
            $packingListImage->packing_list_id = $packingList->id;
            $packingListImage->url = $packingList->attachDocument($image['url'], sprintf('image%s', $i + 1));

            if (! $packingListImage->url) {
                AlertService::alertFail(__('alert.invalidImageFormat'));

                return response()->json(['success' => false], 400);
            }

            $packingListImage->save();
        }

        Order::query()->whereIn('id', $request->orderIds)->update([
            'status' => Order::STATUS_SENT
        ]);

        DB::commit();

        AlertService::alertSuccess(__('alert.processSuccessfully'));

        return response()->json(['success' => true, 'redirect' => route('packing-list.edit', $packingList->uuid)]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
