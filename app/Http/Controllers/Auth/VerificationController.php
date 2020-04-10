<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Auth\Events\Verified;

class VerificationController extends Controller
{
    // use VerifiesEmails;

    /**
     * Show the email verification notice.
     *
     */
    public function show()
    {
        //
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function verify(Request $request, $id)
    {

        // dd("sss");
        // ->route('id') gets route user id and getKey() gets current user id() 
        // do not forget that you must send Authorization header to get the user from the request   
        $user = \App\User::findOrFail($id);

        if ($request->route('id') == $user->getKey() &&
            $user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return Redirect::to('https://almourabi.com');
//        return redirect($this->redirectPath());
    }

    /**
     * Resend the email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json('Email already verified', 422);
//            return redirect($this->redirectPath());
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json('The notification has been resubmitted');
//        return back()->with('resent', true);
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
        // $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }
}