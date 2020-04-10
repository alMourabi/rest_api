<?php

namespace App\Http\Controllers;

use App\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RatingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $ratings = Rating::whereRaw('1=1');

        if (request()->input('video_id'))
            $ratings = $ratings->where(['video_id' => request()->input('video_id')]);

        return $ratings->get();
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
        if (Auth::user()->admin == 0)
            return response()->json(['error' => 'Unauthorized']);

        $validated = Validator::make($request->all(), [
            'rating' => 'integer|required',
            'video_id' => 'required|integer',
            'text' => 'nullable',
        ], [
            'required' => 'L\'attribut :attribute est impératif.',
            'unique' => 'Cet :attribute est déja utilisé.',
            'confirmed' => 'Mot de passe et confirmation différents'
        ]);
        // dd(Lang::locale());
        if ($validated->fails()) {
            return response()->json($validated->messages(), 401);
        }
        $data = $request->all();

        $rating = new Rating($data);
        $rating->user_id = Auth::user()->id;
        $rating->name = Auth::user()->fname." ".Auth::user()->lname;
        if(Rating::where(['user_id'=>$rating->user_id, 'video_id'=>$rating->video_id])->count()!=0)
            return response()->json(['error'=>'Already rated this video'], 400);
        $rating->save();
        return $rating;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Rating  $rating
     * @return \Illuminate\Http\Response
     */
    public function show(Rating $rating)
    {
        //
        return $rating;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Rating  $rating
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Rating $rating)
    {
        //

        if (Auth::user()->admin == 0)
            return response()->json(['error' => 'Unauthorized']);

        $validated = Validator::make($request->all(), [
            'rating' => 'integer|nullable',
            'text' => 'nullable',
        ], [
            'required' => 'L\'attribut :attribute est impératif.',
            'unique' => 'Cet :attribute est déja utilisé.',
            'confirmed' => 'Mot de passe et confirmation différents'
        ]);
        // dd(Lang::locale());
        if ($validated->fails()) {
            return response()->json($validated->messages(), 401);
        }
        $data = $request->only(['rating','text']);
        $data = array_filter($data);

        $rating->update($data);
        $rating->save();
        return $rating;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Rating  $rating
     * @return \Illuminate\Http\Response
     */
    public function destroy(Rating $rating)
    {
        //
        if (Auth::user()->admin != 2 || $rating->user_id!=Auth::user()->id)
            return response()->json(['error' => 'Unauthorized']);

        $rating->delete();
        return response()->json(['success' => '1']);
    }
}
