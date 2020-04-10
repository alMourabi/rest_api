<?php

namespace App\Http\Controllers;

use App\Code;
use Illuminate\Http\Request;
use Auth;


class CodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        if (Auth::user()->admin == 2)
            return Code::all();
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
        if (Auth::user()->admin == 2) {
            if (Code::where(['code' => $request->input('code')])->count() != 0)
                return response()->json(['error' => 'Already exists'], 400);

            $data = $request->all();
            $data['point'] = 10;
            $data['verified'] = 0;
            $code = Code::create($data);
            return $code;
        }
    }

    public function verify()
    {
        if(Code::where(['code' => request()->input('code'), 'verified'=>'0'])->count()==0)
            return response()->json(['error'=>'No such code'], 400);
        $code = Code::where(['code' => request()->input('code')])->update(['user_id'=>Auth::user()->id, 'verified'=>'1']);
        return Code::where(['code' => request()->input('code')])->get();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Classe  $classe
     * @return \Illuminate\Http\Response
     */
    public function edit(Classe $classe)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Classe  $classe
     * @return \Illuminate\Http\Response
     */
    public function destroy(Classe $classe)
    {
        //
    }
}
