<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    public function login(Request $request) : JsonResponse
    {

        $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8|max:255',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'The provided credentails are incorrect'
            ],401);
        }

        $token = $user->createToken($user->name .'Auth-Token')->plainTextToken;

        return response()->json([
                    'message' => 'LOgin Successful',
                    'token_type' => 'Bearer' ,
                    'token' => $token
        ],200);
    }
    public function register(Request $request) : JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users,email|max:255',
                'password' => 'required|string|min:8|max:255',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            if ($user) {
                $token = $user->createToken($user->name . 'Auth-Token')->plainTextToken;

                return response()->json([
                    'message' => 'Registration successful',
                    'token_type' => 'Bearer',
                    'token' => $token
                ], 201);
            } else {
                return response()->json([
                    'message' => 'Something went wrong during registration.',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
    public function logout(Request $request)
{
    $request->user()->tokens()->delete();

    return response()->json([
        'message' => 'Logout successful'
    ]);
}
}
