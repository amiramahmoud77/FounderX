<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('pitch_id')) {
            return Feedback::where('pitch_id', $request->pitch_id)->latest()->get();
        }
        return Feedback::latest()->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pitch_id' => 'required|exists:pitches,id',
            'pdf_file' => 'required|mimes:pdf|max:2048',
        ]);
        $path = $request->file('pdf_file')->store('feedback', 'public');
        $feedback = Feedback::create([
            'pitch_id' => $data['pitch_id'],
            'pdf_path' => $path,
        ]);
        return response()->json($feedback, 201);
    }

    public function show($id)
    {
        return Feedback::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $feedback = Feedback::findOrFail($id);
        $data = $request->validate([
            'pitch_id' => 'sometimes|exists:pitches,id',
            'pdf_file' => 'sometimes|mimes:pdf|max:2048',
        ]);
        if ($request->hasFile('pdf_file')) {
            if ($feedback->pdf_path && Storage::disk('public')->exists($feedback->pdf_path)) {
                Storage::disk('public')->delete($feedback->pdf_path);
            }
            $path = $request->file('pdf_file')->store('feedback', 'public');
            $data['pdf_path'] = $path;
        }
        $feedback->update($data);
        return response()->json($feedback);
    }

    public function destroy($id)
    {
        $feedback = Feedback::findOrFail($id);
        if ($feedback->pdf_path && Storage::disk('public')->exists($feedback->pdf_path)) {
            Storage::disk('public')->delete($feedback->pdf_path);
        }
        $feedback->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}

