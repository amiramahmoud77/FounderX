<?php

namespace App\Http\Controllers;

use App\Models\Pitch;
use App\Models\Score;
use App\Models\User;
use App\Http\Requests\StorePitchRequest;
use App\Http\Requests\UpdatePitchRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PitchController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query= Pitch::with(['user',"score"]);
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

    public function myPitches(Request $request): JsonResponse
    {
        try {
            $pitches= Pitch::where('user_id',Auth::id())->with('score')
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

    public function store(StorePitchRequest $request): JsonResponse
    {
        try {
            $pitch=Pitch::create([
                'title'=>$request->title,
                'problem'=>$request->problem,
                'solution'=>$request->solution,
                'market'=>$request->market,
                'product_tech_stack'=>$request->product_tech_stack,
                'business_model'=>$request->business_model,
                'competition'=>$request->competition,
                'market_strategy'=>$request->market_strategy,
                'traction_results'=>$request->traction_results,
                'team_info'=>$request->team_info,
                'financials_investment'=>$request->financials_investment,
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

    public function show(Pitch $pitch)
    {
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

    public function update(UpdatePitchRequest $request, Pitch $pitch) :JsonResponse
    {
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

    public function destroy(Pitch $pitch):JsonResponse
    {
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

    public function markAsScored(Pitch $pitch) :JsonResponse
    {
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
            $pitches= Pitch::where('status',$status)->with(['user','score'])
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


