<?php

namespace App\Http\Controllers;

use App\ClassPicture;
use Illuminate\Http\Request;

class ClassPictureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return response()->json(['image'=>ClassPicture::where(['class'=>request()->input('class')])->first()->image]);
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
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ClassPicture  $classPicture
     * @return \Illuminate\Http\Response
     */
    public function show(ClassPicture $classPicture)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ClassPicture  $classPicture
     * @return \Illuminate\Http\Response
     */
    public function edit(ClassPicture $classPicture)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ClassPicture  $classPicture
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ClassPicture $classPicture)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ClassPicture  $classPicture
     * @return \Illuminate\Http\Response
     */
    public function destroy(ClassPicture $classPicture)
    {
        //
    }
}
