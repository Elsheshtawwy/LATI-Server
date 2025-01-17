<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $input = $request->validate([
            'name' => ['string', 'required'],
            'phone' => ['required', 'string', 'unique:users,phone', 'min:9', 'max:10'],
            'gender' => ['required', 'in:male,female'],
            'DOB' => ['required', 'date'],
            'password' => ['required', 'string'],
            'avatar_url' => ['nullable']
        ]);
        User::create([
            'name' => $input['name'],
            'phone' => $input['phone'],
            'gender' => $input['gender'],
            'DOB' => $input['DOB'],
            'avatar_url' => $input['avatar_url'] ?? null,
            'password' => Hash::make($input['password'])
        ]);

        return response()->json([
            'data' => 'user registered successfully :)'
        ]);
    }

    public function login(Request $request)
    {
        $credentials = request(['phone', 'password']);
        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function logout()
    {
        auth('api')->logout();

        return response()->json(['data' => 'Successfully logged out']);

    }

    public function user()
    {
        $user = auth()->user();

        return response()->json([
            'data' => $user
        ]);
    }

    public function refresh()
    {
        $token = auth()->refresh();

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);

    }



    public function index(){
        $users = User::all();
        return response()->json([
            'data' => $users
        ]);
    }

    public function delete($id){
        $user = User::find($id);
        if($user){
            $user->delete();
            return response()->json([
               'message' => 'User deleted successfully :('
            ]);
        }
        return response()->json([
           'message' => 'User not found'
        ], 404);
    }


    public function show($id){
        $user = User::find($id);
        if($user){
            return response()->json([
                'data' => $user
            ]);
        }
        return response()->json([
           'message' => 'User not found'
        ], 404);
    }



    public function update($id){
        $user = User::find($id);
        if($user){
            $input = request()->validate([
                'name' => ['string','required'],
                'phone' => ['required','string', 'unique:users,phone,'.$id, 'min:9', 'max:10'],
                'gender' => ['required', 'in:male,female'],
                'DOB' => ['required', 'date'],
                'password' => ['nullable','string'],
                'avatar_url' => ['nullable']
            ]);
            $user->update($input);
            return response()->json([
               'message' => 'User updated successfully'
            ]);
        }
        return response()->json([
           'message' => 'User not found'
        ], 404);
    }
}


