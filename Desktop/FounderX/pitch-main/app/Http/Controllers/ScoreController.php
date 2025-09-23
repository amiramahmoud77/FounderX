<?php

namespace App\Http\Controllers;

use App\Models\Score;
use App\Http\Requests\StoreScoreRequest;
use App\Http\Requests\UpdateScoreRequest;
use App\Models\Pitch;
use App\Models\PitchText;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScoreController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query= Score::with(['pitch',"pitch.user"]);
            if ($request->has("pitch_id")) {
                $query->where('pitch_id',$request->pitch_id);
            }
            $scores=$query->orderBy('created_at','desc')->paginate($request->per_page??10);
            return response()->json([
                'success'=>true,
                'data'=>$scores,
                'message'=>'the scores displayed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to display the scores'.$e->getMessage()
            ],500);
        }
    }

    public function getScoresByPitch(Pitch $pitch):JsonResponse
    {
        try {
            $scores=Score::where('pitch_id',$pitch->id)->with('pitch')->orderBy('created_at','desc')->get();
            return response()->json([
                'success'=>true,
                'data'=>$scores,
                'message'=>'the scores displayed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to display the score'.$e->getMessage()
            ],500);
        }
    }

    public function store(StoreScoreRequest $request): JsonResponse
    {
        try {
            if (!Auth::user()->isAdmin()){
                return response()->json([
                'success'=>false,
                'message'=>'not allowed to update the score'
            ],403);
            }
            $existingscore=Score::where('pitch_id',$request->pitch_id)->first();
            if($existingscore){
                return response()->json([
                'success'=>false,
                'message'=>'the pitches is already scored'
            ],422);
            }
            $score=Score::create($request->validated());
            $score->calcOverAllScore();
            $score->pitch->update(['status'=>'scored']);
            return response()->json([
                'success'=>true,
                'data'=>$score->load('pitch'),
                'message'=>'the scores uploaded successfully'
            ],201);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to upload score '.$e->getMessage()
            ],500);
        }
    }

    public function show(Score $score)
    {
        try {
            $score->load(['pitch','pitch.user']);
            return response()->json([
                'success'=>true,
                'data'=>$score,
                'message'=>'the score displayed successfully'
            ],201);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to display the score'.$e->getMessage()
            ],500);
        }
    }

    public function update(UpdateScoreRequest $request, Score $score) :JsonResponse
    {
        try {
            if (!Auth::user()->isAdmin()){
                return response()->json([
                'success'=>false,
                'message'=>'not allowed to update the score'
            ],403);
            }
            $score->update($request->validated());
            $score->calcOverAllScore();
            return response()->json([
                'success'=>true,
                'data'=>$score->fresh(),
                'message'=>'the score updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to update the score'.$e->getMessage()
            ],500);
        }
    }

    public function destroy(Score $score):JsonResponse
    {
        try {
            if (!Auth::user()->isAdmin()){
                return response()->json([
                'success'=>false,
                'message'=>'not allowed to delete the score'
            ],403);
            }
            $score->pitch->update(['status'=>'submitted']);
            $score->delete();
            return response()->json([
                'success'=>true,
                'message'=>'the score deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to delete the score'.$e->getMessage()
            ],500);
        }
    }

    public function aiScore(Request $request):JsonResponse
    {
        try{
            $request->validate([
                'type'=>'required|in:pitch,pitch-text',
                'id'=>'required|integer',
                'scores'=>'required|array',
            ]);
            $type=$request->type;
            $id=$request->id;
            $scores=$request->scores;
            if ($type === 'pitch') {
                $pitch=Pitch::findOrFail($id);
                $existingScore=Score::where('pitch_id',$id)->first();
                if ($existingScore){
                    return response()->json([
                        'success'=>false,
                        'message'=>'pitch is already scored'
                    ],422);
                    $scoreData=array_merge($scores,['pitch_id'=>$id]);
                    $score=Score::create($scoreData);
                    $score->calcOverAllScore();
                    $pitch->update(['status'=>'scored']);
                    return response()->json([
                        'success'=>true,
                        'data'=>$score->load('pitch'),
                        'message'=>'the scores uploaded successfully'
                    ],201);
                }
            } elseif ($type === 'pitch-text') {
                $pitchText=PitchText::findOrFail($id);
                $existingScore=Score::where('pitch_text_id',$id)->first();
                if ($existingScore){
                    return response()->json([
                        'success'=>false,
                        'message'=>'pitch is already scored'
                    ],402);
                    $scoreData=array_merge($scores,['pitch_text_id'=>$id]);
                    $score=Score::create($scoreData);
                    $score->calcOverAllScore();
                    $pitchText->update(['status'=>'scored']);
                    return response()->json([
                        'success'=>true,
                        'data'=>$score->load('pitch_text'),
                        'message'=>'the scores uploaded successfully'
                    ],201);
                }
            }
        }catch (\Exception $e){
            return response()->json([
                'success'=>false,
                'message'=>'failed to process the score'.$e->getMessage()
            ],500);
        }
    }
}
