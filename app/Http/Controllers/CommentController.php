<?php

namespace App\Http\Controllers;

use App\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $comments = Comment::whereRaw('1=1');
        if (request()->input('video_id'))
            $comments = $comments->where(['video_id' => request()->input('video_id')]);
        return $comments->get();
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
            'video_id'=>'required|integer', 
            'text'=>'required', 
            // 'comment_id'=>'nullable|integer',
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
        $comment = new Comment($data);
        $comment->name = Auth::user()->fname." ".Auth::user()->lname;
        $comment->user_id = Auth::user()->id;

        $comment->save();
        return $comment;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $comment)
    {
        //
        return $comment;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comment $comment)
    {
        //

        if (Auth::user()->admin == 0)
            return response()->json(['error' => 'Unauthorized']);

        // $validated = Validator::make($request->all(), [
        //     'comment' => 'integer|nullable',
        //     'video_id' => 'nullable',
        //     'text' => 'nullable',
        // ], [
        //     'required' => 'L\'attribut :attribute est impératif.',
        //     'unique' => 'Cet :attribute est déja utilisé.',
        //     'confirmed' => 'Mot de passe et confirmation différents'
        // ]);
        // dd(Lang::locale());
        // if ($validated->fails()) {
        //     return response()->json($validated->messages(), 401);
        // }
        $data = $request->only(['text']);

        $comment->update($data);
        $comment->save();
        return $comment;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        //
        if (Auth::user()->admin != 2 || $comment->user_id!=Auth::user()->id)
            return response()->json(['error' => 'Unauthorized']);

				foreach($comment->comments as $c)
					$this->destroy($c);		
        $comment->delete();
        return response()->json(['success' => '1']);
    }
}
