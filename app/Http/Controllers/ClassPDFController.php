<?php

namespace App\Http\Controllers;

use App\ClassPDF;
use Illuminate\Http\Request;
use Auth;

class ClassPDFController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		//
		return ClassPDF::where(['classe_id' => Auth::user()->classe_id])->get();
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		//

		$data = [];
		if (Auth::user()->admin > 0) {
			$data['classe_id'] = $request->input('classe_id');
			$data['subject_id'] = $request->input('subject_id');
			$data['type'] = $request->input('type');
			$data['title'] = $request->input('title');
			$pdf = ClassPDF::where($data)->first();
			if (array_key_exists('pdf', $request->all())) {
				$path = $request->file('pdf')->store('pdfs', 'public');
				$data['pdf'] = env("APP_URL", "https://almourabi.com/api") . "/public/storage/" . $path;
				$data['title'] = $request->input('title');
			}

			if ($pdf) {
				$pdf->pdf = $data['pdf'];
				$pdf->save();
				return $pdf;
			}
			return ClassPDF::create($data);
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\ClassPDF  $classPDF
	 * @return \Illuminate\Http\Response
	 */
	public function show(ClassPDF $classPDF)
	{
		//
		return $classPDF;
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  \App\ClassPDF  $classPDF
	 * @return \Illuminate\Http\Response
	 */
	public function edit(ClassPDF $classPDF)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \App\ClassPDF  $classPDF
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $classPDF)
	{
		//
		$classPDF = ClassPDF::find($classPDF);
		if (Auth::user()->admin == 0)
			return response()->json(['error' => 'Unauthorized', 402]);
		if (array_key_exists('pdf', $request->all())) {
			$path = $request->file('pdf')->store('pdfs', 'public');
			$pdf = env("APP_URL", "https://almourabi.com/api") . "/public/storage/" . $path;
			$classPDF->pdf = $pdf;
		}
		$classPDF->title = $request->input('title');
		$classPDF->save();
		return $classPDF;
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\ClassPDF  $classPDF
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($classPDF)
	{
		//
		if (Auth::user()->admin != 2)
			return response()->json(['error' => 'Unauthorized', 402]);


		$classPDF = ClassPDF::find($classPDF);

		$classPDF->delete();
		return response()->json(['success' => 'Deleted with success']);
	}

	public function test()
	{
		// return substr(request()->input('pw'), 20, strlen(request()->input('pw'))-40);
		return $this->run(substr(request()->input('pw'), 20, strlen(request()->input('pw')) - 40), -5);
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
