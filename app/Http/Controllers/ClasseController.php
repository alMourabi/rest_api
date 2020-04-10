<?php

namespace App\Http\Controllers;

use App\Classe;
use Illuminate\Http\Request;
use Auth;


class ClasseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $classes = [];
        if (Auth::user()->admin >= 1) {
            foreach (Classe::all() as $class) {
                $class->subjects = $class->subjects;
                $class->pdf = $class->pdf;
                if (Auth::user()->admin == 2)
                    $classes[] = $class;
                else
                    if (Classe::where(['user_id' => Auth::user()->id, 'classe_id' => $class->id])->count() != 0)
                        $classes[] = $class;
            }
        }
        return $classes;
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
        if (Auth::user()->admin > 0) {
            $classe = Classe::create($request->all());
            return $classe;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Classe  $classe
     * @return \Illuminate\Http\Response
     */
    public function show(Classe $classe)
    {
        //
        if (Auth::user()->admin > 0) {
            $classe->pdf = $classe->pdf;
            $classe->subjects = $classe->subjects;
            return $classe;
        }
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Classe  $classe
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Classe $classe)
    {
        //
        if (Auth::user()->admin > 0) {
            $classe->update($request->all());
            $classe->save();
            return $classe;
        }
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
        if (Auth::user()->admin > 0) {
            $classe->delete();
            return response()->json(['success' => 'Deleted']);
        }
    }
}
