<?php

namespace App\Http\Controllers;

use App\Models\Stage;
use App\Http\Requests\StoreStageRequest;
use App\Http\Requests\UpdateStageRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query= Stage::query();
            if ($request->has('with')) {
                $relations =explode(',',$request->with);
                $query->with($relations);
            }
            $stages=$query->orderBy('created_at','desc')->paginate($request->per_page??10);
            return response()->json([
                'success'=>true,
                'data'=>$stages,
                'message'=>'the field displayed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to display the field'.$e->getMessage()
            ],500);
        }
    }

    public function store(StoreStageRequest $request):JsonResponse
    {
        try {
            if (!Auth::user()->isAdmin()){
                return response()->json([
                'success'=>false,
                'message'=>'not allowed to update the field'
            ],403);
            }
            $stage=Stage::create([
                'stage'=>$request->stage,
            ]);
            return response()->json([
                'success'=>true,
                'data'=>$stage,
                'message'=>'the field uploaded successfully'
            ],201);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to display the field'.$e->getMessage()
            ],500);
        }
    }

    public function show(Stage $stage):JsonResponse
    {
        try {
            $stage->load(['pitches','pitchTexts']);
            return response()->json([
                'success'=>true,
                'data'=>$stage,
                'message'=>'the field displayed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to display the field'.$e->getMessage()
            ],500);
        }
    }

    public function update(UpdateStageRequest $request, Stage $stage):JsonResponse
    {
        try {
            if (!Auth::user()->isAdmin()){
                return response()->json([
                'success'=>false,
                'message'=>'not allowed to update the field'
            ],403);
            }
            $stage->update(['stage'=>$request->stage]);
            return response()->json([
                'success'=>true,
                'data'=>$stage->fresh(),
                'message'=>'the field updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to update the field'.$e->getMessage()
            ],500);
        }
    }

    public function destroy(Stage $stage):JsonResponse
    {
        try {
            if (!Auth::user()->isAdmin()){
                return response()->json([
                'success'=>false,
                'message'=>'not allowed to delete the field'
            ],403);
            }
            if ($stage->pitches()->count()>0 ||$stage->pitchTexts()->count()>0){
                return response()->json([
                'success'=>false,
                'message'=>'not allowed to delete the field'
            ],403);
            }
            $stage->delete();
            return response()->json([
                'success'=>true,
                'message'=>'the field deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to delete the field'.$e->getMessage()
            ],500);
        }
    }

    public function get_pitches_by_stages($stageId, Request $request):JsonResponse
    {
        try {
            $stage=Stage::findOrFail($stageId);
            $pitches=$stage->pitches()->with(['user','score'])
            ->orderBy('created_at','desc')
            ->paginate($request->par_page??10);
            return response()->json([
                'success'=>true,
                'data'=>$pitches,
                'message'=>'fieldsInvestors displayed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to display the team'.$e->getMessage()
            ],500);
        }
    }

    public function get_pitchText_by_stages($stageId, Request $request):JsonResponse
    {
        try {
            $stage=Stage::findOrFail($stageId);
            $pitchTexts=$stage->pitchTexts->with(['user','score'])
            ->orderBy('created_at','desc')
            ->paginate($request->par_page??10);
            return response()->json([
                'success'=>true,
                'data'=>$pitchTexts,
                'message'=>'fieldsInvestors displayed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to display the team'.$e->getMessage()
            ],500);
        }
    }

}

