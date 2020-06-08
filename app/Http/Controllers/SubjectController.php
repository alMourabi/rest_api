<?php

namespace App\Http\Controllers;

use App\Subject;
use App\Classe;
use App\User;
use App\UserSubjectClasse;
use Illuminate\Http\Request;
use Auth;
use Exception;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
				//
				
				$subjects = Subject::where(['classe_id'=>Auth::user()->classe_id])->get();
				foreach($subjects as $subject)
					$subject->videos = $subject->videos;
            return $subjects;
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
        if(Auth::user()->admin>0){
            $subject = Subject::create($request->all());
            return $subject;
        } 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function show(Subject $subject)
    {
        //
						$subject->pdf = $subject->pdf;
						$subject->videos = $subject->videos;
            return $subject;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function edit(Subject $subject)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Subject $subject)
    {
        //
        if(Auth::user()->admin>0){
            $subject->update($request->all());
            $subject->save();
            return $subject;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subject $subject)
    {
        //
        if(Auth::user()->admin>0){
            $subject->delete();
            return response()->json(['success'=>'Deleted']);
        }
		}
		
		public function link(){
			if(Auth::user()->admin==2){
				$subjects = json_decode(request()->input('subjects'), true);
                $user = User::where(['email'=>request()->input('email')])->first();
                if($user==null)
                    return response()->json(['error'=>'No user with that email'], 400);
				$err = [];
				foreach($subjects as $sub){
					try{
						$tmp = $sub;
						$sub = Subject::findOrFail($sub);
						$s = UserSubjectClasse::where(['subject_id'=>$sub->id, 'classe_id'=>$sub->classe_id, 'user_id'=>$user->id])->first();
						if($s){
							$err[] = "Subject with id ".$sub->id." already already linked to user";
							continue;
						}
						$s = UserSubjectClasse::create(['subject_id'=>$sub->id, 'classe_id'=>$sub->classe_id, 'user_id'=>$user->id]);
					}
					catch(Exception $e){
						$err[] = $tmp." is not a valid subject id";
					}
				}
				return response()->json(['success'=>'Linked successfully', 'warnings'=>$err]);
			}
		}

		public function unlink(){
			if(Auth::user()->admin==2){
				$subjects = json_decode(request()->input('subjects'), true);
				$user = User::where(['email'=>request()->input('email')])->get()[0];
				$err = [];
				foreach($subjects as $sub){
					try{
						$tmp = $sub;
						$sub = Subject::findOrFail($sub);
						$s = UserSubjectClasse::where(['subject_id'=>$sub->id, 'classe_id'=>$sub->classe_id, 'user_id'=>$user->id])->first();
						if($s){
							$s->delete();
						}
					}
					catch(Exception $e){
					}
				}
				return response()->json(['success'=>'Unlinked successfully']);
			}
		}
}
