<?php

namespace App\Http\Controllers;

use App\Courier;
use App\Customer;
use App\Sector;
use App\Service\AlertService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
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
        $customers = Customer::query()
            ->search($request->search)
            ->my()
            ->with(['defaultCourier', 'customerEmails'])
            ->paginate()
        ;

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $customers]);
        }

        return view('customer.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $sectors = Sector::all();

        return view('customer.form', compact('sectors'));
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

        $customer = new Customer($request->all());
        $customer->save();

        $customer->updateCustomerEmails($request->customer_emails);
        $customer->updateCustomerRates($request->customer_rates);

        DB::commit();

        AlertService::alertSuccess(__('alert.processSuccessfully'));

        return response()->json(['success' => true, 'redirect' => route('customer.index')]);
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
        $sectors = Sector::all();
        $customer = Customer::query()
            ->uuid($id)
            ->my()
            ->with(['defaultCourier', 'customerEmails', 'seller', 'customerRates.courier'])
            ->firstOrFail()
        ;

        return view('customer.form', compact('customer', 'sectors'));
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

        $customer = Customer::query()->uuid($id)->firstOrFail();
        $customer->fill($request->all());
        $customer->save();

        $customer->updateCustomerEmails($request->customer_emails);
        $customer->updateCustomerRates($request->customer_rates);

        DB::commit();

        AlertService::alertSuccess(__('alert.processSuccessfully'));

        return response()->json(['success' => true, 'redirect' => route('customer.edit', $id)]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer = Customer::query()->uuid($id)->firstOrFail();
        $customer->delete();

        AlertService::alertSuccess(__('alert.processSuccessfully'));

        return response()->json(['success' => true, 'redirect' => route('customer.index')]);
    }
}
