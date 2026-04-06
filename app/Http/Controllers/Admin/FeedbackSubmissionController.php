<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FeedbackSubmissionStatusUpdateRequest;
use App\Models\FeedbackSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeedbackSubmissionController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', ''));

        $submissions = FeedbackSubmission::query()
            ->with(['type:id,name'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($q2) use ($q) {
                    $q2->where('subject', 'like', '%' . $q . '%')
                        ->orWhere('name', 'like', '%' . $q . '%')
                        ->orWhere('email', 'like', '%' . $q . '%');
                });
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $statusOptions = FeedbackSubmission::statusOptions();

        return view('admin.feedback.submissions.index', compact('submissions', 'q', 'status', 'statusOptions'));
    }

    public function show(FeedbackSubmission $feedbackSubmission): View
    {
        $feedbackSubmission->loadMissing('type:id,name');

        return view('admin.feedback.submissions.show', [
            'submission' => $feedbackSubmission,
            'statusOptions' => FeedbackSubmission::statusOptions(),
        ]);
    }

    public function updateStatus(
        FeedbackSubmissionStatusUpdateRequest $request,
        FeedbackSubmission $feedbackSubmission
    ): RedirectResponse {
        $feedbackSubmission->update([
            'status' => (string) $request->validated('status'),
        ]);

        return back()->with('success', 'Feedback status updated.');
    }
}

