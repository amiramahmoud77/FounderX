<?php

namespace App\Http\Controllers;

use App\Models\StagesInvestors;
use App\Http\Requests\StoreStagesInvestorsRequest;
use App\Http\Requests\UpdateStagesInvestorsRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StagesInvestorsController extends Controller
{
    public function index(Request $request):JsonResponse{
        try {
            if (!Auth::user()->isInvestor()){
                return response()->json([
                    'success'=>false,
                    'message'=>'not allowed to view fields & investors'
                ],403);
            }
            $query =StagesInvestors::with(['investor','stage']);
            if (Auth::user()->isInvestor() && !Auth::user()->isAdmin()){
                $query->where('investor_id',Auth::user()->investor_id);
            }
            if ($request->has('investor_id') && Auth::user()->isAdmin()){
                $query->where('investor_id',Auth::user()->investor_id);
            }
            if ($request->has('investor_id')){
                $query->where('investor_id',Auth::user()->investor_id);
            }
            if ($request->has('stage_id')){
                $query->where('stage_id',$request->stage_id);
            }
            $stagesInvestors=$query->orderBy('created_at','desc')->paginate($request->per_page??10);
            return response()->json([
                'success'=>true,
                'data'=>$stagesInvestors,
                'message'=>'stagesInvestors displayed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to display the team'.$e->getMessage()
            ],500);
        }
    }

    public function store(StoreStagesInvestorsRequest $request):JsonResponse
    {
        try {
            if (!Auth::user()->isInvestor() && !Auth::user()->isAdmin()){
                return response()->json([
                    'success'=>false,
                    'message'=>'not allowed to create fields & investors'
                ],403);
            }
            if (!Auth::user()->isInvestor() && !Auth::user()->isAdmin()){
                $investorId=Auth::user()->investor_id;
                if($request->investor_id!=$investorId){
                    return response()->json([
                    'success'=>false,
                    'message'=>'not allowed to create fields & investors'
                ],403);
                }
            }
            $existingRelation=StagesInvestors::where("investor_id",$request->investor_id)->where('stage_id',$request->stage_id)->first();
            if($existingRelation){
                    return response()->json([
                    'success'=>false,
                    'message'=>'not allowed to create fields & investors already exists'
                ],422);
                }
            $stagesInvestors=StagesInvestors::create($request->validated());
            return response()->json([
                'success'=>true,
                'data'=>$stagesInvestors->load(['investor','stage']),
                'message'=>'fieldsInvestors displayed successfully'
            ],201);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to create'.$e->getMessage()
            ],500);
        }
    }

    public function show(StagesInvestors $stagesInvestors):JsonResponse
    {
        try {
            if (Auth::user()->isInvestor()&&!Auth::user()->isAdmin()&&Auth::user()->investor_id!==$stagesInvestors->stage_id){
                return response()->json([
                    'success'=>false,
                    'message'=>'not allowed to view fields & investors'
                ],403);
            }
            $stagesInvestors->load(['investor','stage']);
            return response()->json([
                'success'=>true,
                'data'=>$stagesInvestors,
                'message'=>'stagesInvestors displayed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to display the team'.$e->getMessage()
            ],500);
        }
    }

    public function update(UpdateStagesInvestorsRequest $request, StagesInvestors $stagesInvestors):JsonResponse
    {
        try {
            if (!Auth::user()->isInvestor() && !Auth::user()->isAdmin()){
                return response()->json([
                    'success'=>false,
                    'message'=>'not allowed to create fields & investors'
                ],403);
            }
            $stagesInvestors->update($request->validated());
            return response()->json([
                'success'=>true,
                'data'=>$stagesInvestors->fresh()->load(['investor','field']),
                'message'=>'stagesInvestors displayed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to create'.$e->getMessage()
            ],500);
        }
    }

    public function destroy(StagesInvestors $stagesInvestors):JsonResponse
    {
        try {
            if ((!Auth::user()->isInvestor()||
            Auth::user()->investor_id!==$stagesInvestors->investor_id)
            && !Auth::user()->isAdmin()){
                return response()->json([
                    'success'=>false,
                    'message'=>'not allowed to create fields & investors'
                ],403);
            }
            $stagesInvestors->delete();
            return response()->json([
                'success'=>true,
                'message'=>'fieldsInvestors displayed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to create'.$e->getMessage()
            ],500);
        }
    }

    public function get_stages_investor($investorId, Request $request):JsonResponse
    {
        try {
            if (Auth::user()->isInvestor()&&(!Auth::user()->isAdmin()&&Auth::user()->investor_id!= $investorId)){
                return response()->json([
                    'success'=>false,
                    'message'=>'not allowed to view stages & investors'
                ],403);
            }
            $stages=StagesInvestors::where('investor_id',$investorId)->with('stage')->orderBy('created_at','desc')->paginate($request->par_page??10);
            return response()->json([
                'success'=>true,
                'data'=>$stages,
                'message'=>'stagesInvestors displayed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to display the team'.$e->getMessage()
            ],500);
        }
    }

    public function get_all_stages_investor($investorId, Request $request):JsonResponse
    {
        try {
            if (!Auth::user()->isAdmin()){
                return response()->json([
                    'success'=>false,
                    'message'=>'not allowed to view fields & investors'
                ],403);
            }
            $stages=StagesInvestors::where('investor_id',$investorId)->with('stage')->orderBy('created_at','desc')->paginate($request->par_page??10);
            return response()->json([
                'success'=>true,
                'data'=>$stages,
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
