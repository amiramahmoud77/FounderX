<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Http\Requests\StoreFieldRequest;
use App\Http\Requests\UpdateFieldRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FieldController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query= Field::with(['pitch']);
            $field=$query->orderBy('created_at','desc')->paginate($request->per_page??10);
            return response()->json([
                'success'=>true,
                'data'=>$field,
                'message'=>'the field displayed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to display the field'.$e->getMessage()
            ],500);
        }
    }

    public function store(StoreFieldRequest $request): JsonResponse
    {
        try {
            $field=Field::create([
                'name'=>$request->name,
            ]);
            return response()->json([
                'success'=>true,
                'data'=>$field,
                'message'=>'the field uploaded successfully'
            ],201);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to display the field'.$e->getMessage()
            ],500);
        }
    }

    public function show(Field $field)
    {
        try {
            $field->load(['pitches']);
            return response()->json([
                'success'=>true,
                'data'=>$field,
                'message'=>'the field displayed successfully'
            ],201);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to display the field'.$e->getMessage()
            ],500);
        }
    }

    public function update(UpdateFieldRequest $request, Field $field) :JsonResponse
    {
        try {
            if (Auth::id()!==$field->user_id && !Auth::user()->isAdmin()){
                return response()->json([
                'success'=>false,
                'message'=>'not allowed to update the field'
            ],403);
            }
            $field->update($request->validated());
            return response()->json([
                'success'=>true,
                'data'=>$field->fresh(),
                'message'=>'the field updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to update the field'.$e->getMessage()
            ],500);
        }
    }

    public function destroy(Field $field):JsonResponse
    {
        try {
            if (Auth::id()!==$field->user_id && !Auth::user()->isAdmin()){
                return response()->json([
                'success'=>false,
                'message'=>'not allowed to delete the field'
            ],403);
            }
            $field->delete();
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

}
