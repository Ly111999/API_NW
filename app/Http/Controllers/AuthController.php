<?php

namespace App\Http\Controllers;

use App\Notifications\PasswordResetRequest;
use App\Notifications\SignupActivate;
use App\PasswordReset;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Create user
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */
    public function register(Request $request)
    {
        //validate
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed'
        ]);
        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'activation_token' => str_random(60)
        ]);

        $user->save();
//        $user->notify(new SignupActivate($user));

        return response()->json([
            'success' => true,
            'message' => "Created User success !"
        ], 201);
    }

    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {
        //$credentials: thông tin đăng nhập
        $credentials = request(['email', 'password']);
//        $credentials['active'] = 1;

        if (!Auth::attempt($credentials))
            return response()->json([
                'success' => false,
                'error' => 'Invalid email or password'
            ], 401);
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        //thời gian token sống 24h
        $token->expires_at = Carbon::now('Asia/Ho_Chi_Minh')->addHours(24);

        //nếu check remember me thì token sống 1 tuần
        if ($request->remember_me)
            $token->expires_at = Carbon::now('Asia/Ho_Chi_Minh')->addWeeks(1);
        $token->save();
        return response()->json([
            'success' => true,
            'token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function signupActivate($token)
    {
        $user = User::where('activation_token', $token)->first();
        if (!$user) {
            return response()->json([
                'message' => 'This activation token is invalid.'
            ], 404);
        }
        $user->active = true;
        $user->activation_token = '';
        $user->email_verified_at = Carbon::now();
        $user->save();
        return "Success";
    }

    public function getData()
    {
        $user = User::all();
        if (count($user) == 0) {
            return response()->json(['error_message' => "No item found"], 200);
        } else {
            return response()->json(['data' => $user], 200);
        }
    }

    public function destroy(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Sorry, user cannot be found'
            ], 200);
        }
        $user->delete();
        return response()->json([
            'status' => true,
            'message' => 'Deleted success'
        ], 201);
    }


}


