<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserCenter;
use App\Models\Center;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'designation' => 'required',
            'base_hospital' => 'required',
            'campus' => 'required',
            'slmc_reg_no' => 'required',
            'password' => 'required|min:6',
            'center' => 'required',
        ]);

        /** @var \App\Models\User $user */
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'designation' => $request->input('designation'),
            'base_hospital' => $request->input('base_hospital'),
            'campus' => $request->input('campus'),
            'slmc_reg_no' => $request->input('slmc_reg_no'),
            'user_type' => 2,
            'password' => bcrypt($request->input('password'))
        ]);


        $userCenter = new UserCenter;

        $userCenter->center_id = $request->input('center');
        $userCenter->user_id = $user->id;
        $userCenter->is_active =  true;
        $userCenter->created_by = $user->id;
        $userCenter->save();

        $token = $user->createToken('main')->plainTextToken;

        $center = Center::findOrFail($request->input('center')); // Retrieve the center model
        $user->load('centers');
        $user->load('userType');

        return response([
            'user' => $user,
            'center' => $center,
            'token' => $token
        ]);
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
            'center' => 'required', // Add the validation rule for the center selection
        ]);

        $credentials = $request->only('email', 'password');
        $centerId = $request->input('center');

        $remember = $credentials['remember'] ?? false;
        unset($credentials['remember']);

        if (!Auth::attempt($credentials, $remember)) {
            return response([
                'error' => 'The Provided credentials are not correct'
            ], 422);
        }
        $user = Auth::user();
        $token = $user->createToken('main')->plainTextToken;
        $user->load('centers');
        // $user->load('userType');

        // dd($user->centers);

        if ($user->centers->contains('id', $centerId)) {
            // Center is acceptable, proceed with login
            return response([
                'user' => $user,
                'center' => $request->input('center'),
                'token' => $token
            ]);
        } else {
            // Center is not acceptable, show an error message
            return response(['center' => 'Selected center is not acceptable.']);
        }
    }

    public function logout(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        // Revoke the token that was used to authenticate the current request...
        //$user->currentAccessToken()->delete();

        return response([
            'success' => true
        ]);
    }

    public function me(Request $request)
    {
        return $request->user();
    }

    public function getAll(Request $request)
    {
        return User::with('userType', 'centers')
            ->get();
    }
}
