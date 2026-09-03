<?php

namespace App\Notifications;

use App\Models\AppointmentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Emailed to the practice the moment a patient submits the contact form.
 *
 * Sent synchronously on purpose. Most buyers of this template run on shared
 * hosting with no queue worker, and a queued mail there would sit in the `jobs`
 * table forever with nobody noticing. If you *do* run `php artisan queue:work`,
 * add `implements ShouldQueue` to the class below and the mail moves onto the
 * queue with no other change:
 *
 *     use Illuminate\Contracts\Queue\ShouldQueue;
 *     class NewAppointmentRequestNotification extends Notification implements ShouldQueue
 *
 * Which mail service is used is entirely a .env matter (MAIL_MAILER=smtp,
 * postmark, resend, ses…) — nothing here needs to change to swap providers.
 */
class NewAppointmentRequestNotification extends Notification
{
    use Queueable;

    public function __construct(
        public AppointmentRequest $appointmentRequest,
    ) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $request = $this->appointmentRequest;

        $mail = (new MailMessage)
            ->subject("New appointment request from {$request->patient_name}")
            ->greeting('You have a new appointment request')
            ->line("**{$request->patient_name}** would like to book an appointment.")
            ->line('**Preferred date:** '.$request->preferred_date->format('l j F Y'))
            ->line('**Preferred time:** '.$request->timeSlotLabel())
            ->line('**Phone:** '.$request->phone);

        if (filled($request->email)) {
            $mail->line('**Email:** '.$request->email);
        }

        if (filled($request->message)) {
            $mail->line('**Their message:** '.$request->message);
        }

        return $mail
            ->action('Open it in the admin panel', url('/admin/appointment-requests/'.$request->id))
            ->line('Confirm or reject the request from the admin panel, then call the patient back.');
    }
}
