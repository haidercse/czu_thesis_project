<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;

class DocumentReviewController extends Controller
{
    public function index()
    {
        $documents = Document::with('user', 'application.program.university')
            ->latest()
            ->paginate(20);

        return view('backend.pages.documents.review', compact('documents'));
    }

    public function review(Request $request, Document $document)
    {
        $this->authorize('review', $document);

        $validated = $request->validate([
            'review_status' => 'required|string|in:uploaded,in_review,approved,rejected,resubmission',
            'review_comment' => 'required|string|min:10|max:2000',
        ]);

        $document->update([
            'review_status' => $validated['review_status'],
            'review_comment' => $validated['review_comment'] ?? null,
        ]);

        if (! $request->expectsJson()) {
            return redirect()
                ->route('admin.documents.review.index')
                ->with('success', 'Document review updated.');
        }

        return response()->json([
            'success' => true,
            'message' => 'Document review updated.',
            'data' => $document->fresh(),
        ]);
    }
}
