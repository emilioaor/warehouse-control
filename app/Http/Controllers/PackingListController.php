<?php

namespace App\Http\Controllers;

use App\Exports\PackingListExport;
use App\Order;
use App\PackingList;
use App\PackingListImage;
use App\Service\AlertService;
use App\Transport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

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
            ->pending()
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
        $transports = Transport::all();
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

        return view('packing-list.form', compact('packingList', 'transports'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        $packingList = PackingList::query()->uuid($id)->firstOrFail();
        $packingList->status = PackingList::STATUS_SENT;
        $packingList->received_at = new Carbon();
        $packingList->transport_id = $request->transport_id;
        $packingList->sign = $packingList->attachDocument($request->sign, 'sign');

        if (! $packingList->sign) {
            AlertService::alertFail(__('alert.invalidImageFormat'));

            return response()->json(['success' => false], 400);
        }

        $packingList->save();

        if (! $packingList->updateImages($request->packing_list_images)) {

            AlertService::alertFail(__('alert.invalidImageFormat'));

            return response()->json(['success' => false], 400);
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

    /**
     * Report
     *
     * @return \Illuminate\Http\Response
     */
    public function report()
    {
        return view('packing-list.report');
    }

    /**
     * Report order
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function reportProcess(Request $request)
    {
        $orders = PackingList::query()->report($request->all())->get();

        return response()->json(['success' => true, 'data' => $orders]);
    }

    /**
     * Send email
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendEmail($id, Request $request)
    {
        $packingList = PackingList::query()
            ->with(['orders.orderDetails', 'orders.customer', 'courier'])
            ->uuid($id)
            ->firstOrFail()
        ;

        Mail::to($request->emails)->send(new \App\Mail\PackingList($packingList));

        AlertService::alertSuccess(__('alert.processSuccessfully'));

        return response()->json(['success' => true]);
    }

    /**
     * Show PDF
     */
    public function pdf($id)
    {
        $packingList = PackingList::query()->uuid($id)->firstOrFail();
        $packingList->completeInfoPDF();
        $pdf = \PDF::loadView('pdf.packingList', ['packingList' => $packingList])->setPaper('letter');

        return $pdf->stream();
    }

    /**
     * Export excel
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function excel($id)
    {
        $packingList = PackingList::query()->uuid($id)->firstOrFail();

        return Excel::download(new PackingListExport($packingList), 'packing-list-' . $id . '.xlsx');
    }

    /**
     * Update images
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function images(Request $request, $id)
    {
        DB::beginTransaction();

        $packingList = PackingList::query()->uuid($id)->firstOrFail();

        if (! $packingList->updateImages($request->packing_list_images)) {
            return response()->json(['success' => false], 400);
        }

        DB::commit();

        AlertService::alertSuccess(__('alert.processSuccessfully'));

        return response()->json(['success' => true, 'redirect' => route('packing-list.edit', $packingList->uuid)]);
    }
}
