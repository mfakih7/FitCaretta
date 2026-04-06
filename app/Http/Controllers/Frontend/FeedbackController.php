<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\FeedbackSubmissionStoreRequest;
use App\Mail\FeedbackSubmittedMail;
use App\Models\FeedbackSetting;
use App\Models\FeedbackSubmission;
use App\Models\FeedbackType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    public function create(): View
    {
        $this->abortIfDisabled();

        $types = FeedbackType::query()->where('is_active', true)->orderBy('name')->get();

        return view('frontend.feedback.create', compact('types'));
    }

    public function store(FeedbackSubmissionStoreRequest $request): RedirectResponse
    {
        $this->abortIfDisabled();

        $payload = $request->validated();

        $screenshotPath = null;
        if ($request->hasFile('screenshot')) {
            $screenshotPath = $request->file('screenshot')->store('feedback-screenshots', 'public');
        }

        /** @var \App\Models\FeedbackSubmission $submission */
        $submission = FeedbackSubmission::query()->create([
            'name' => $payload['name'],
            'email' => $payload['email'],
            'feedback_type_id' => (int) $payload['feedback_type_id'],
            'subject' => $payload['subject'],
            'message' => $payload['message'],
            'screenshot_path' => $screenshotPath,
            'page_url' => $payload['page_url'] ?? null,
            'status' => FeedbackSubmission::STATUS_NEW,
        ]);

        $submission->loadMissing('type:id,name');

        $adminEmailSent = true;
        try {
            $to = (string) config('store.contact_email', '');
            if ($to !== '') {
                Mail::to($to)->send(new FeedbackSubmittedMail($submission));
            }
        } catch (\Throwable $e) {
            $adminEmailSent = false;
            Log::error('Feedback email failed', [
                'feedback_id' => $submission->id,
                'to' => (string) config('store.contact_email', ''),
                'exception' => get_class($e),
                'error' => $e->getMessage(),
            ]);
        }

        if (! $adminEmailSent) {
            return back()
                ->withInput()
                ->with('warning', 'Feedback saved, but email notification failed.');
        }

        return redirect()->route('feedback.create')->with('success', 'Thank you, your feedback has been submitted successfully.');
    }

    private function abortIfDisabled(): void
    {
        if (! Schema::hasTable('feedback_settings')) {
            abort(404);
        }

        $enabled = (bool) FeedbackSetting::query()->value('is_enabled');
        abort_unless($enabled, 404);
    }
}

