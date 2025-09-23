<?php

namespace App\Http\Controllers;

use App\Models\FieldsInvestors;
use App\Http\Requests\StoreFieldsInvestorsRequest;
use App\Http\Requests\UpdateFieldsInvestorsRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FieldsInvestorsController extends Controller
{
    public function index(Request $request):JsonResponse
    {
        try {
            if (!Auth::user()->isInvestor()){
                return response()->json([
                    'success'=>false,
                    'message'=>'not allowed to view fields & investors'
                ],403);
            }
            $query =FieldsInvestors::with(['investor','field']);
            if (Auth::user()->isInvestor() && !Auth::user()->isAdmin()){
                $query->where('investor_id',Auth::user()->investor_id);
            }
            if ($request->has('investor_id') && Auth::user()->isAdmin()){
                $query->where('investor_id',$request->investor_id);
            }
            if ($request->has('investor_id')){
                $query->where('investor_id',Auth::user()->investor_id);
            }
            if ($request->has('field_id')){
                $query->where('field_id',$request->field_id);
            }
            $fieldsInvestors=$query->orderBy('created_at','desc')->paginate($request->per_page??10);
            return response()->json([
                'success'=>true,
                'data'=>$fieldsInvestors,
                'message'=>'fieldsInvestors displayed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to display the team'.$e->getMessage()
            ],500);
        }
    }

    public function store(StoreFieldsInvestorsRequest $request):JsonResponse
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
            $existingRelation=FieldsInvestors::where("investor_id",$request->investor_id)->where('field_id',$request->field_id)->first();
            if($existingRelation){
                    return response()->json([
                    'success'=>false,
                    'message'=>'not allowed to create fields & investors already exists'
                ],422);
                }
            $fieldsInvestors=FieldsInvestors::create($request->validated());
            return response()->json([
                'success'=>true,
                'data'=>$fieldsInvestors->load(['investor','field']),
                'message'=>'fieldsInvestors displayed successfully'
            ],201);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to create'.$e->getMessage()
            ],500);
        }
    }

    public function show(FieldsInvestors $fieldsInvestors):JsonResponse
    {
        try {
            if (Auth::user()->isInvestor()&&!Auth::user()->isAdmin()&&Auth::user()->investor_id!==$fieldsInvestors->investor_id){
                return response()->json([
                    'success'=>false,
                    'message'=>'not allowed to view fields & investors'
                ],403);
            }
            $fieldsInvestors=load(['investor','field']);
            return response()->json([
                'success'=>true,
                'data'=>$fieldsInvestors,
                'message'=>'fieldsInvestors displayed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to display the team'.$e->getMessage()
            ],500);
        }
    }

    public function update(UpdateFieldsInvestorsRequest $request, FieldsInvestors $fieldsInvestors):JsonResponse
    {
        try {
            if (!Auth::user()->isInvestor() && !Auth::user()->isAdmin()){
                return response()->json([
                    'success'=>false,
                    'message'=>'not allowed to create fields & investors'
                ],403);
            }
            $fieldsInvestors->update($request->validated());
            return response()->json([
                'success'=>true,
                'data'=>$fieldsInvestors->fresh()->load(['investor','field']),
                'message'=>'fieldsInvestors displayed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to create'.$e->getMessage()
            ],500);
        }
    }

    public function destroy(FieldsInvestors $fieldsInvestors):JsonResponse
    {
        try {
            if ((!Auth::user()->isInvestor()||Auth::user()->investor_id!==$fieldsInvestors->investor_id) && !Auth::user()->isAdmin()){
                return response()->json([
                    'success'=>false,
                    'message'=>'not allowed to create fields & investors'
                ],403);
            }
            $fieldsInvestors->delete();
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

    public function get_fields_investor($investorId, Request $request):JsonResponse
    {
        try {
            if (Auth::user()->isInvestor()&&(!Auth::user()->isAdmin()&&Auth::user()->investor_id!= $investorId)){
                return response()->json([
                    'success'=>false,
                    'message'=>'not allowed to view fields & investors'
                ],403);
            }
            $fields=FieldsInvestors::where('investor_id',$investorId)->with('field')->orderBy('created_at','desc')->paginate($request->par_page??10);
            return response()->json([
                'success'=>true,
                'data'=>$fields,
                'message'=>'fieldsInvestors displayed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to display the team'.$e->getMessage()
            ],500);
        }
    }

    public function get_all_fields_investor($investorId, Request $request):JsonResponse
    {
        try {
            if (!Auth::user()->isAdmin()){
                return response()->json([
                    'success'=>false,
                    'message'=>'not allowed to view fields & investors'
                ],403);
            }
            $fields=FieldsInvestors::where('investor_id',$investorId)->with('field')->orderBy('created_at','desc')->paginate($request->par_page??10);
            return response()->json([
                'success'=>true,
                'data'=>$fields,
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

