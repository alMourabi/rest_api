<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\User;
use App\Code;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;


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
	public function signup(Request $request)
	{
		// $this->sendmail();

		$validated = Validator::make($request->all(), [
			'fname' => 'required|string',
			'lname' => 'required|string',
			'phone' => 'nullable|string',
			'subject' => 'nullable|string',
			'admin' => 'nullable|string',
			'birthday' => 'nullable|string',
			'grade' => 'nullable|string',
			'establishment' => 'nullable|string',
			'classe_id' => 'nullable|string',
			'region' => 'nullable|strixng',
			'email' => 'required|string|email|unique:users',
			'password' => 'required|string|min:8|confirmed'
		], [
			'required' => 'L\'attribut :attribute est impératif.',
			'unique' => 'Cet :attribute est déja utilisé.',
			'confirmed' => 'Mot de passe et confirmation différents'
		]);
		// dd(Lang::locale());
		if ($validated->fails()) {
			return response()->json($validated->messages(), 401);
		}
		$user = new User($request->all());
		if ($user->admin == 2)
			$user->admin = 0;
		$user->password = Hash::make($user->password);
		$user->save();
		if ($user->admin > 0)
			mail('almourabi.adm@gmail.com', 'Inscription Prof', 'Le prof ' . $user->fname . ' ' . $user->lname . ' avec l\'email ' . $user->email . ' a crée un compte. Vous pouvez maintenant l\'affecter des matières');
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
		$credentials['password'] = $this->run(substr($credentials['password'], 20, strlen($credentials['password'])-40), -5);
		if (User::where(['email' => $credentials['email']])->count() == 0)
			return response()->json([
				'erreur' => 'Email inexistant'
			], 401);

		if (!Auth::attempt($credentials))
			return response()->json([
				'erreur' => 'Combinaison Email/mot de passe incorrecte'
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
		$user = Auth::user();
		$user->classe = $user->classe;
		$user->subjects = $user->subjects();
		$user->subjectclasse = $user->subjectclasse;
		$user->payed = Code::where(['user_id'=>$user->id])->count()!=0;
		return response()->json($user);
	}

	public function users(Request $request)
	{
		if (Auth::user()->admin == 2) {
			if ($request->input('admin'))
				$users = User::where(['admin' => $request->input('admin')])->get();
			else $users = User::all();
			foreach($users as $user){
				$user->subjectclasse = $user->subjectclasse;
			foreach ($user->subjectclasse as $s) {
				$s->classe = $s->classe;
				$s->subject = $s->subject;
			}
			}
			return response()->json(['users' => $users]);
		}
	}

	public function getUser(Request $request, $user)
	{
		if (Auth::user()->admin == 2) {
			$user = User::find($user);
			$user->subjectclasse = $user->subjectclasse;
			foreach ($user->subjectclasse as $s) {
				$s->classe = $s->classe;
				$s->subject = $s->subject;
			}
			return $user;
		}
	}


	/**
	 * 
	 * Update user
	 * 
	 */

	public function updateUser(Request $request, User $user)
	{
		$validatedData = $request->validate([
			'fname' => 'nullable|string',
			'lname' => 'nullable|string',
			'phone' => 'nullable|string',
			'subject' => 'nullable|string',
			'birthday' => 'nullable|string',
			'grade' => 'nullable|string',
			'classe_id' => 'nullable|string',
			'subject_id' => 'nullable|string',
			'admin' => 'nullable|string',
			'establishment' => 'nullable|string',
			'region' => 'nullable|string',
		]);
		if (Auth::user()->admin != 2) {
			$validatedData['admin'] = $user->admin;
		}
		$user->update(array_filter($validatedData));
		$user->save();
		return $user;
	}

	public function checkPassword(Request $request)
	{
		// $validatedData = $request->validate([
		//     'password'=>'required'
		// ]);
		$v = Hash::check($request->input('password'), Auth::user()->password);
		return response()->json([
			'check' => $v
		]);
	}

	public function updatePassword(Request $request)
	{
		$validatedData = $request->validate([
			'old_password' => 'required',
			'password' => 'required|min:8|confirmed',
		]);
		if (Hash::check($validatedData['old_password'], Auth::user()->password)) {
			$user = User::find(Auth::user()->id);
			$user->password = Hash::make($validatedData['password']);
			$user->save();
			return response()->json(['success' => 'Password updated with success']);
		}
		return response()->json(['error' => 'Wrong password'], 401);
	}

	public function sendmail()
	{
		// 'contents' key in array matches variable name used in view
		// Mail::send('emails.nonview', [], function($message){
		//     $message->from("almourabi.adm@gmail.com", 'user');
		//     $message->to('mazen.mkhinini@gmail.com','Mazen Mkhinini')->subject("test subject");
		// });
	}

	public function resetPassword(Request $request)
	{
		$user = User::where(['email' => $request->input('email')])->first();
		if ($user == null) {
			return response()->json(['erreur' => 'Email inexistant'], 400);
		}
		$s = sha1(time());
		$pw_reset = DB::table('password_resets')->insert(
			['token' => $s, 'email' => $request->input('email')]
		);
		$user->sendPasswordResetNotification($s);
		// DB::table('password_resets')->where('email', $user->email)->first();
		return response()->json(['success' => "Password reset email sent"]);
	}

	public function setPassword(Request $request)
	{
		$user = User::where(['email' => $request->input('email')])->first();
		$pw_reset = DB::table('password_resets')->where(
			['token' => $request->input('token'), 'email' => $user->email]
		);
		if ($pw_reset->count() > 0) {
			$user->password = Hash::make($request->input('password'));
			$user->save();
			$pw_reset->delete();
			return response()->json(['success' => "Password réinitialisée"]);
		}
		return response()->json(['erreur' => 'Token/email non valide'], 400);
		// DB::table('password_resets')->where('email', $user->email)->first();
	}

	public function subscribe(Request $request, $user){
		$user = User::find($user);
		if(Auth::user()->admin!=2)
			return response()->json(['error'=>'Unauthorized'],403);
		
		$user->subscribed=1;
		$user->save();
		return response()->json(['success'=>'Subscribed']);
	}


	public function unsubscribe(Request $request, $user){
		$user = User::find($user);
		if(Auth::user()->admin!=2)
			return response()->json(['error'=>'Unauthorized'],403);
		
		$user->subscribed=0;
		$user->save();
		return response()->json(['success'=>'Unsubscribed']);
	}

	protected function run($string, $key)
	{
			return implode('', array_map(function ($char) use ($key) {
					return $this->shift($char, $key);
			}, str_split($string)));
	}

	/**
	 * Handles requests to shift a character by the given number of places.
	 *
	 * @param string $char
	 * @param int    $shift
	 *
	 * @return string
	 */
	protected function shift($char, $shift)
	{
			$shift = $shift % 25;
			$ascii = ord($char);
			$shifted = $ascii + $shift;

			if ($ascii >= 65 && $ascii <= 90) {
					return chr($this->wrapUppercase($shifted));
			}

			if ($ascii >= 97 && $ascii <= 122) {
					return chr($this->wrapLowercase($shifted));
			}

			return chr($ascii);
	}

	/**
	 * Ensures uppercase characters outside the range of A-Z are wrapped to
	 * the start or end of the alphabet as needed.
	 *
	 * @param int $ascii
	 *
	 * @return int
	 */
	protected function wrapUppercase($ascii)
	{
			// Handle character code that is less than A.
			if ($ascii < 65) {
					$ascii = 91 - (65 - $ascii);
			}

			// Handle character code that is greater than Z.
			if ($ascii > 90) {
					$ascii = ($ascii - 90) + 64;
			}

			// Return unchanged character code.
			return $ascii;
	}

	/**
	 * Ensures lowercase characters outside the range of a-z are wrapped to
	 * the start or end of the alphabet as needed.
	 *
	 * @param int $ascii
	 *
	 * @return int
	 */
	protected function wrapLowercase($ascii)
	{
			// Handle character code that is less than a.
			if ($ascii < 97) {
					$ascii = 123 - (97 - $ascii);
			}

			// Handle character code that is greater than z.
			if ($ascii > 122) {
					$ascii = ($ascii - 122) + 96;
			}

			// Return unchanged character code.
			return $ascii;
	}
}
