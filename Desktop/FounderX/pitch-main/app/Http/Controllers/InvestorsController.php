<?php

namespace App\Http\Controllers;

use App\Models\Investors;
use Illuminate\Http\Request;

class InvestorsController extends Controller
{
    public function index()
    {
        return Investors::latest()->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'focus_field' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'min_charge' => 'required|integer|min:0',
            'max_charge' => 'required|integer|min:0|gte:min_charge',
            'user_id' => 'required|exists:users,id',
        ]);

        $inv = Investors::create($data);
        return response()->json($inv, 201);
    }

    public function show($id)
    {
        return Investors::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'focus_field' => 'sometimes|string|max:255',
            'company' => 'sometimes|string|max:255',
            'min_charge' => 'sometimes|integer|min:0',
            'max_charge' => 'sometimes|integer|min:0|gte:min_charge',
            'user_id' => 'sometimes|exists:users,id',
        ]);

        $inv = Investors::findOrFail($id);
        $inv->update($data);

        return response()->json($inv);
    }

    public function destroy($id)
    {
        Investors::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}

