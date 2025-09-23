<?php

namespace App\Http\Controllers;

use App\Models\PitchText;
use App\Http\Requests\StorePitchTextRequest;
use App\Http\Requests\UpdatePitchTextRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PitchTextController extends Controller
{
    public function index(Request $request): JsonResponse{
        try {
            $query= PitchText::with(['user',"score"]);
            if ($request->has("status")) {
                $query->where('status',$request->status);
            }
            if ($request->has("user_id")) {
                $query->where('user_id',$request->user_id);
            }
            $pitches=$query->orderBy('created_at','desc')->paginate($request->per_page??10);
            return response()->json([
                'success'=>true,
                'data'=>$pitches,
                'message'=>'the pitches displayed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to display the pitches'.$e->getMessage()
            ],500);
        }
    }

    public function myPitches(Request $request): JsonResponse{
        try {
            $pitches= PitchText::where('user_id',Auth::id())->with('score')
                ->orderBy('created_at','desc')->paginate($request->per_page??10);
            return response()->json([
                'success'=>true,
                'data'=>$pitches,
                'message'=>'the pitches displayed successfully'
            ],201);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to display the pitches'.$e->getMessage()
            ],500);
        }
    }

    public function store(StorePitchTextRequest $request): JsonResponse{
        try {
            $pitch=PitchText::create([
                'text'=>$request->text,
                'status'=>$request->status,
                'field_id'=>$request->field_id,
                'stage_id'=>$request->stage_id,
                'user_id'=>Auth::id(),
            ]);
            return response()->json([
                'success'=>true,
                'data'=>$pitch->load('user'),
                'message'=>'the pitches uploaded successfully'
            ],201);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to display the pitches'.$e->getMessage()
            ],500);
        }
    }

    public function show(PitchText $pitch){
        try {
            $pitch->load(['user','score']);
            return response()->json([
                'success'=>true,
                'data'=>$pitch,
                'message'=>'the pitch displayed successfully'
            ],201);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to display the pitch'.$e->getMessage()
            ],500);
        }
    }

    public function update(UpdatePitchTextRequest $request, PitchText $pitch) :JsonResponse{
        try {
            if (Auth::id()!==$pitch->user_id && !Auth::user()->isAdmin()){
                return response()->json([
                'success'=>false,
                'message'=>'not allowed to update the pitch'
            ],403);
            }
            $pitch->update($request->validated());
            return response()->json([
                'success'=>true,
                'data'=>$pitch->fresh(),
                'message'=>'the pitch updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to update the pitch'.$e->getMessage()
            ],500);
        }
    }

    public function destroy(PitchText $pitch):JsonResponse{
        try {
            if (Auth::id()!==$pitch->user_id && !Auth::user()->isAdmin()){
                return response()->json([
                'success'=>false,
                'message'=>'not allowed to delete the pitch'
            ],403);
            }
            $pitch->delete();
            return response()->json([
                'success'=>true,
                'message'=>'the pitch deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to delete the pitch'.$e->getMessage()
            ],500);
        }
    }

    public function markAsScored(PitchText $pitch) :JsonResponse{
        try {
            if (Auth::id()===$pitch->user_id || Auth::user()->isAdmin()){
                $pitch->update(['status'=>"scored"]);
                return response()->json([
                'success'=>true,
                'message'=>'the status changed to scored'
            ]);
            }
            return response()->json([
                'success'=>false,
                'message'=>'not allowed to change status'
            ],403);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to change the status'.$e->getMessage()
            ],500);
        }
    }

    public function getByStatus($status,Request $request){
        try {
            $validStatuses=['draft','submitted','scored'];
            if (!in_array($status,$validStatuses)){
                return response()->json([
                'success'=>false,
                'message'=>'the status is invalid'
            ],400);
            }
            $pitches= PitchText::where('status',$status)->with(['user','score'])
                ->orderBy('created_at','desc')->paginate($request->per_page??10);
            return response()->json([
                'success'=>true,
                'data'=>$pitches,
                'message'=>'status is displyed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to display the status'.$e->getMessage()
            ],500);
        }
    }
}
