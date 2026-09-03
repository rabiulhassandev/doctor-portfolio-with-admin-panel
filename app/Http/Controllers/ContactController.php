<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Models\AppointmentRequest;
use App\Models\DoctorProfile;
use App\Notifications\NewAppointmentRequestNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Throwable;

/** The contact page and the appointment request form it carries. */
class ContactController extends Controller
{
    public function index(): View
    {
        return view('pages.contact');
    }

    /**
     * Save an appointment request and let the practice know about it.
     *
     * Validation happens in {@see StoreAppointmentRequest} before this method
     * runs, so everything in $request->validated() is already safe.
     */
    public function store(StoreAppointmentRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // The honeypot field is only there to catch bots — it is never stored.
        unset($data['website']);

        $appointmentRequest = AppointmentRequest::create($data);

        $this->notifyPractice($appointmentRequest);

        return redirect()
            ->route('contact')
            // Anchor straight back to the form so the patient sees the message.
            ->withFragment('appointment')
            ->with('appointment_submitted', true);
    }

    /**
     * Email the practice.
     *
     * Wrapped in a try/catch on purpose: if the buyer has not finished setting
     * up their mail credentials, the patient should still get a confirmation
     * and the request must still be saved. The failure is logged so a developer
     * can find it in storage/logs.
     */
    private function notifyPractice(AppointmentRequest $appointmentRequest): void
    {
        $recipient = DoctorProfile::current()->email
            ?: config('mail.from.address');

        if (blank($recipient)) {
            return;
        }

        try {
            Notification::route('mail', $recipient)
                ->notify(new NewAppointmentRequestNotification($appointmentRequest));
        } catch (Throwable $e) {
            Log::error('Could not email the new appointment request.', [
                'appointment_request_id' => $appointmentRequest->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
