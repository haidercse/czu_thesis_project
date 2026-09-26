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

        $application = auth()->user()->applications()->latest()->first();

        if ($application) {
            $existingDocuments = $application->documents()
                ->where('document_type', $request->document_type)
                ->get();

            foreach ($existingDocuments as $existingDocument) {
                \Storage::disk('local')->delete($existingDocument->file_path);
                $existingDocument->delete();
            }
        }

        $document = Document::create([
            'user_id' => auth()->id(),
            'application_id' => $application?->id,
            'document_type' => $request->document_type,
            'original_filename' => $request->file('file')->getClientOriginalName(),
            'stored_filename' => $storedName,
            'file_path' => $path,
            'file_size' => $request->file('file')->getSize(),
        ]);

        $matchingStep = null;

        if ($application) {
            $application->load('steps.step');

            $matchingStep = $application->steps->first(function ($step) use ($request) {
                return $step->step
                    && $step->step->related_document_type === $request->document_type
                    && in_array($step->status, ['not_started', 'in_progress'], true);
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
        $this->authorize('view', $document);

        return response()->download(storage_path('app/' . $document->file_path), $document->original_filename);
    }

    public function view(Document $document)
    {
        $this->authorize('view', $document);

        $path = \Storage::disk('local')->path($document->file_path);

        abort_unless(is_file($path), 404);

        return response()->file($path, [
            'Content-Disposition' => 'inline; filename=' . str_replace(['"', "\r", "\n"], '', $document->original_filename),
        ]);
    }

    public function destroy(Document $document)
    {
        $this->authorize('delete', $document);

        \Storage::disk('local')->delete($document->file_path);
        $document->delete();
        return response()->json(['success' => true]);
    }
}
