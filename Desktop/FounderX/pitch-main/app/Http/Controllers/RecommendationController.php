<?php

namespace App\Http\Controllers;

use App\Models\Recommendation;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    public function index()
    {
        return Recommendation::with(['score', 'investor'])->latest()->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'score_id' => 'required|exists:scores,id',
            'investor_id' => 'required|exists:investors,id',
            'match_score' => 'required|integer|min:0|max:100',
        ]);

        $rec = Recommendation::create($data);
        return response()->json($rec, 201);
    }

    public function show($id)
    {
        return Recommendation::with(['score', 'investor'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $rec = Recommendation::findOrFail($id);

        $data = $request->validate([
            'score_id' => 'sometimes|exists:scores,id',
            'investor_id' => 'sometimes|exists:investors,id',
            'match_score' => 'sometimes|integer|min:0|max:100',
        ]);

        $rec->update($data);
        return response()->json($rec);
    }

    public function destroy($id)
    {
        Recommendation::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
