<?php

namespace App\Http\Controllers;

use App\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $videos = Video::whereRaw('1=1');
        if (request()->input('classe_id'))
            $videos = $videos->where(['classe_id' => request()->input('classe_id')]);

        if (request()->input('subject_id'))
            $videos = $videos->where(['subject_id' => request()->input('subject_id')]);

        if (request()->input('user_id'))
            $videos = $videos->where(['user_id' => request()->input('user_id')]);

        if (request()->input('payed'))
            $videos = $videos->where(['payed' => request()->input('payed')]);

        $videos = $videos->get();
        foreach($videos as $video){
            $video->pdf=$video->pdf;
            $video->comments=$video->comments;
            $video->ratings=$video->ratings;
        }

        return $videos;
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
        if(Auth::user()->admin==0)
            return response()->json(['error'=>'Unauthorized']);

        $validated = Validator::make($request->all(), [
            'title' => 'required',
            'video'=>'file',
            'payed' => 'nullable',
            'description' => 'nullable',
            'subject_id' => 'required|integer',
            'classe_id' => 'required|integer',
            'type'=>'required'
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

        $video = new Video($data);
        $video->user_id = Auth::user()->id;
        
        if (array_key_exists('video', $data)) {
            $path = $request->file('video')->store('videos', 'local');
            $data['url'] = env("APP_URL", "https://almourabi.com/api") . "/public/api/" . $path;
        }
        else
            return response()->json(['error'=>'Please include a video file'], 401);

				if (array_key_exists('thumbnail', $data)) {
							$path = $request->file('thumbnail')->store('thumbnails', 'public');
							$data['thumbnail'] = env("APP_URL", "https://almourabi.com/api") . "/public/storage/" . $path;
							$video->thumbnail = $data['thumbnail'];
					}

        $video->url = $data['url'];
        $video->save();
        return $video;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function show(Video $video)
    {
        //
        if(!Auth::user()->subscribed && $video->payed)
            return response()->json(['error'=>'Ce video n\'est disponible que pour les membres abonnées.']);

        $video->pdf=$video->pdf;
        $video->comments=$video->comments;
        $video->ratings=$video->ratings;
        
        return $video;
    }

    public function download($video){
        // // dd(request()->ip());
        // if(request()->ip() != "127.0.0.1")
        //     return 'No video available';

        return response()->file(storage_path().'/app/videos/'.$video, ['Content-Type'=>'video/mp4']);        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Video $video)
    {
        //

        if(Auth::user()->admin==0)
            return response()->json(['error'=>'Unauthorized']);

        $validated = Validator::make($request->all(), [
            'title' => 'nullable',
            'video'=>'nullable|file',
            'payed' => 'nullable',
            'description' => 'nullable',
            'subject_id' => 'nullable|integer',
            'classe_id' => 'nullable|integer',
            'type'=>'nullable'
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
        $data = array_filter($data);
        
        if (array_key_exists('video', $data)) {
            $path = $request->file('video')->store('videos', 'local');
            $data['url'] = env("APP_URL", "https://almourabi.com/api") . "/public/api/" . $path;
            $video->url = $data['url'];
        }
        $video->update($data);
        $video->save();
        return $video;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function destroy(Video $video)
    {
        //
        if(Auth::user()->admin!=2)
        return response()->json(['error'=>'Unauthorized']);
				$ctrl = new CommentController();
				foreach($video->comments as $c)
					$ctrl->destroy($c);
				$ctrl = new RatingController();
				foreach($video->ratings as $r)
						$ctrl->destroy($r);
        $video->delete();
        return response()->json(['success'=>'1']);
    }
}
