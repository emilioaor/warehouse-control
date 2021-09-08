<?php

namespace App\Http\Controllers;

use App\Service\AlertService;
use App\Transport;
use Illuminate\Http\Request;

class TransportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $transports = Transport::query()
            ->search($request->search)
            ->paginate()
        ;

        return view('transport.index', compact('transports'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('transport.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $transport = new Transport($request->all());
        $transport->save();

        AlertService::alertSuccess(__('alert.processSuccessfully'));

        return response()->json(['success' => true, 'redirect' => route('transport.index')]);
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
        $transport = Transport::query()->uuid($id)->firstOrFail();

        return view('transport.form', compact('transport'));
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
        $transport = Transport::query()->uuid($id)->firstOrFail();
        $transport->fill($request->all());
        $transport->save();

        AlertService::alertSuccess(__('alert.processSuccessfully'));

        return response()->json(['success' => true, 'redirect' => route('transport.edit', [$id])]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $transport = Transport::query()->uuid($id)->firstOrFail();
        $transport->delete();

        AlertService::alertSuccess(__('alert.processSuccessfully'));

        return response()->json(['success' => true, 'redirect' => route('transport.index')]);
    }
}
