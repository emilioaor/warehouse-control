<?php

namespace App\Http\Controllers;

use App\Courier;
use App\Service\AlertService;
use Illuminate\Http\Request;

class CourierController extends Controller
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
        $couriers = Courier::query()->search($request->search)->paginate();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $couriers]);
        }

        return view('courier.index', compact('couriers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('courier.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $courier = new Courier($request->all());
        $courier->save();

        AlertService::alertSuccess(__('alert.processSuccessfully'));

        return response()->json(['success' => true, 'redirect' => route('courier.index')]);
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
        $courier = Courier::query()->uuid($id)->firstOrFail();

        return view('courier.form', compact('courier'));
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
        $courier = Courier::query()->uuid($id)->firstOrFail();
        $courier->fill($request->all());
        $courier->save();

        AlertService::alertSuccess(__('alert.processSuccessfully'));

        return response()->json(['success' => true, 'redirect' => route('courier.edit', $id)]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $courier = Courier::query()->uuid($id)->firstOrFail();
        $courier->delete();

        AlertService::alertSuccess(__('alert.processSuccessfully'));

        return response()->json(['success' => true, 'redirect' => route('courier.index')]);
    }
}
