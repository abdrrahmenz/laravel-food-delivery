<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    //user register
    public function userRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        $data['roles'] = 'user';

        $user = User::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully',
            'data' => $user
        ]);
    }

    //get user
    public function getUser(Request $request)
    {
        // Check for Authorization header
        if (!$request->hasHeader('Authorization')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // check header Authorization token sanctum not valid
        if (!$request->user()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $request->user()
        ]);
    }

    //user login
    public function userLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials'
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'User logged in successfully',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ]);
    }

    //user logout
    public function userLogout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User logged out successfully'
        ]);
    }

    //restaurant register
    public function restaurantRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string',
            'phone' => 'required|string',
            'restaurant_name' => 'required|string',
            'restaurant_address' => 'required|string',
            'photo' => 'required|image',
            'latlong' => 'required|string',
        ], [
            'name.required' => 'The name field is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'The email has already been taken.',
            'password.required' => 'The password field is required.',
            'phone.required' => 'The phone field is required.',
            'restaurant_name.required' => 'The restaurant name field is required.',
            'restaurant_address.required' => 'The restaurant address field is required.',
            'photo.required' => 'The photo field is required.',
            'latlong.required' => 'The latlong field is required.',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        $data['roles'] = 'restaurant';

        $user = User::create($data);

        //check if photo is uploaded
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photo_name = time() . '.' . $photo->getClientOriginalExtension();
            $photo->move(public_path('images'), $photo_name);
            $user->photo = $photo_name;
            $user->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Restaurant registered successfully',
            'data' => $user
        ]);
    }

    //driver register
    public function driverRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string',
            'phone' => 'required|string',
            'license_plate' => 'required|string',
            'photo' => 'required|image',
        ], [
            'name.required' => 'The name field is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'The email has already been taken.',
            'password.required' => 'The password field is required.',
            'phone.required' => 'The phone field is required.',
            'license_plate.required' => 'The license plate field is required.',
            'photo.required' => 'The photo field is required.',
        ]);


        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        $data['roles'] = 'driver';

        $user = User::create($data);

        //check if photo is uploaded
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photo_name = time() . '.' . $photo->getClientOriginalExtension();
            $photo->move(public_path('images'), $photo_name);
            $user->photo = $photo_name;
            $user->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Driver registered successfully',
            'data' => $user
        ]);
    }

    //update latlong user
    public function updateLatlong(Request $request)
    {
        $request->validate([
            'latlong' => 'required|string',
        ]);

        $user = $request->user();
        $user->latlong = $request->latlong;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Latlong updated successfully',
            'data' => $user
        ]);
    }

    //get all restaurant
    public function getRestaurant()
    {
        $restaurant = User::where('roles', 'restaurant')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Get all restaurant',
            'data' => $restaurant
        ]);
    }
}
