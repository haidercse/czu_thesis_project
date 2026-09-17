<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = auth()->user()->documents()->latest()->get();
        return view('frontend.documents', compact('documents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB
            'document_type' => 'required|string',
        ]);

        $storedName = Str::random(40) . '.' . $request->file('file')->extension();
        $path = $request->file('file')->storeAs('documents/' . auth()->id(), $storedName, 'local');

        $document = Document::create([
            'user_id' => auth()->id(),
            'document_type' => $request->document_type,
            'original_filename' => $request->file('file')->getClientOriginalName(),
            'stored_filename' => $storedName,
            'file_path' => $path,
            'file_size' => $request->file('file')->getSize(),
        ]);

        $matchingStep = null;
        $application = auth()->user()->applications()->latest()->first();

        if ($application) {
            $application->load('steps.step');

            $matchingStep = $application->steps->first(function ($step) use ($request) {
                return $step->step
                    && $step->step->related_document_type === $request->document_type
                    && $step->status === 'not_started';
            });

            if ($matchingStep) {
                $matchingStep->update(['status' => 'in_progress']);
            }
        }

        return response()->json([
            'success' => true,
            'document' => $document,
            'checklist_updated' => (bool) $matchingStep,
        ]);
    }

    public function download(Document $document)
    {
        if ($document->user_id !== auth()->id()) {
            abort(403);
        }
        return response()->download(storage_path('app/' . $document->file_path), $document->original_filename);
    }

    public function destroy(Document $document)
    {
        if ($document->user_id !== auth()->id()) {
            abort(403);
        }
        \Storage::disk('local')->delete($document->file_path);
        $document->delete();
        return response()->json(['success' => true]);
    }
}
