<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Http\Requests\StoreTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Models\Pitch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query= Team::with(['pitch']);
            $team=$query->orderBy('created_at','desc')->paginate($request->per_page??10);
            return response()->json([
                'success'=>true,
                'data'=>$team,
                'message'=>'the team displayed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to display the team'.$e->getMessage()
            ],500);
        }
    }

    public function store(StoreTeamRequest $request): JsonResponse
    {
        try {
            $pitch=Pitch::findOrFail($request->pitch_id);
            if (Auth::id()!==$pitch->user_id && !Auth::user()->isAdmin()){
                return response()->json([
                'success'=>false,
                'message'=>'not allowed to add the team'
            ],403);
            }
            $team=Team::create([
                'name'=>$request->name,
                'background'=>$request->background,
                'role'=>$request->role,
                'pitch_id'=>$request->pitch_id,
            ]);
            return response()->json([
                'success'=>true,
                'data'=>$team->load('pitch'),
                'message'=>'the team uploaded successfully'
            ],201);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to display the team'.$e->getMessage()
            ],500);
        }
    }

    public function show(Team $team)
    {
        try {
            $team->load(['pitch']);
            return response()->json([
                'success'=>true,
                'data'=>$team,
                'message'=>'the team displayed successfully'
            ],201);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to display the team'.$e->getMessage()
            ],500);
        }
    }

    public function update(UpdateTeamRequest $request, Team $team) :JsonResponse
    {
        try {
            $pitch=$team->pitch;
            if (Auth::id()!==$pitch->user_id && !Auth::user()->isAdmin()){
                return response()->json([
                'success'=>false,
                'message'=>'not allowed to update the team'
            ],403);
            }
            $team->update($request->validated());
            return response()->json([
                'success'=>true,
                'data'=>$team->fresh(),
                'message'=>'the team updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to update the team'.$e->getMessage()
            ],500);
        }
    }

    public function destroy(Team $team):JsonResponse
    {
        try {
            $pitch=$team->pitch;
            if (Auth::id()!==$pitch->user_id && !Auth::user()->isAdmin()){
                return response()->json([
                'success'=>false,
                'message'=>'not allowed to delete the team'
            ],403);
            }
            $team->delete();
            return response()->json([
                'success'=>true,
                'message'=>'the team deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'failed to delete the team'.$e->getMessage()
            ],500);
        }
    }

}
