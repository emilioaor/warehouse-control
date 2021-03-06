<?php

namespace App\Http\Controllers;

use App\Service\AlertService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::query()->notMe()->search($request->get('search'))->paginate();

        return view('user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = new User($request->all());
        $user->password = bcrypt($request->password);
        $user->save();

        AlertService::alertSuccess(__('alert.processSuccessfully'));

        return response()->json(['success' => true, 'redirect' => route('user.index')]);
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
        $user = User::query()->uuid($id)->firstOrFail();

        return view('user.form', compact('user'));
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
        $user = User::query()->uuid($id)->firstOrFail();
        $user->fill($request->all());

        if (! empty($request->password)) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        AlertService::alertSuccess(__('alert.processSuccessfully'));

        return response()->json(['success' => true, 'redirect' => route('user.edit', $id)]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::query()->uuid($id)->firstOrFail();
        $user->delete();

        AlertService::alertSuccess(__('alert.processSuccessfully'));

        return response()->json(['success' => true, 'redirect' => route('user.index')]);
    }

    /**
     * Check if user exists
     *
     * @param  string $email
     * @return \Illuminate\Http\Response
     */
    public function userExists($email)
    {
        $user = User::query()->where('email', $email)->first();

        return response()->json(['success' => true, 'data' => $user]);
    }

    /**
     * Configuration
     *
     * @return \Illuminate\Http\Response
     */
    public function config()
    {
        return view('user.config');
    }

    /**
     * Check if user exists
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateConfig(Request $request)
    {
        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->getAuthPassword())) {
            AlertService::alertFail(__('alert.invalidCurrentPassword'));

            return response()->json(['success' => false, 'code' => 'invalid_password'], 400);
        }

        $user->password = bcrypt($request->password);
        $user->save();

        AlertService::alertSuccess(__('alert.processSuccessfully'));

        return response()->json(['success' => true, 'redirect' => route('user.config')]);
    }

    /**
     * Sellers
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sellers(Request $request)
    {
        $sellers = User::query()->search($request->get('search'))->where('role', User::ROLE_SELLER)->paginate();

        return response()->json(['success' => true, 'data' => $sellers]);
    }
}
