<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Create user
     *
     * @param  [string] fname
     * @param  [string] lname
     * @param  [string] phone
     * @param  [string] birthday
     * @param  [string] grade
     * @param  [string] establishment
     * @param  [string] region
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */
    public function signup(CreateUser $request)
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);
        $user = new User($validated);
        $user->save();
        $user->sendEmailVerificationNotification();
        return response()->json([
            'message' => 'Successfully created user!'
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
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);
        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials))
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(56);
        $token->save();
        return response()->json([
            'access_token' => $tokenResult->accessToken,
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


    /**
     * 
     * Update user
     * 
     */

    public function updateUser(Request $request, User $user){
        $validatedData = $request->validate([
            'fname' => 'nullable|string',
            'lname' => 'nullable|string',
            'phone' => 'nullable|string',
            'birthday' => 'nullable|string',
            'grade' => 'nullable|string',
            'establishment' => 'nullable|string',
            'region' => 'nullable|string',
        ]);
        $user->update(array_filter($validatedData));
        $user->save();
        return $user;
    }

    public function checkPassword(Request $request){
        // $validatedData = $request->validate([
        //     'password'=>'required'
        // ]);
        $v = Hash::check($request->input('password'), Auth::user()->password);
        return response()->json([
            'check'=>$v
        ]);

    }

    public function updatePassword(Request $request){
        $validatedData = $request->validate([
            'old_password'=>'required',
            'password'=>'required|min:8|confirmed',
        ]);
        if(Hash::check($validatedData['old_password'],Auth::user()->password)){
            $user = User::find(Auth::user()->id);
            $user->password = Hash::make($validatedData['password']);
            $user->save();
            return response()->json(['success'=>'Password updated with success']);
        }
        return response()->json(['error'=>'Wrong password'],401);
    }
}
