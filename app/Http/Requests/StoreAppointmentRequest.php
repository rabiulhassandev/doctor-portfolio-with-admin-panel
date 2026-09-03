<?php

namespace App\Http\Requests;

use App\Models\AppointmentRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Server-side validation for the public appointment form.
 *
 * Everything the patient types is checked here — never trust the browser, which
 * a determined visitor can bypass entirely.
 */
class StoreAppointmentRequest extends FormRequest
{
    /** The form is public: anyone may submit it. */
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'patient_name' => ['required', 'string', 'max:255'],

            // Either an email or a phone is enough to reply, but a phone is the
            // one the practice actually calls, so that is the required one.
            'phone' => ['required', 'string', 'max:40'],
            'email' => ['nullable', 'email:rfc', 'max:255'],

            'preferred_date' => ['required', 'date', 'after_or_equal:today'],
            'preferred_time' => ['required', Rule::in(array_keys(AppointmentRequest::TIME_SLOTS))],
            'message' => ['nullable', 'string', 'max:2000'],

            // Honeypot: a hidden field real patients never see, so anything that
            // fills it in is a bot. `size:0` means it must be empty.
            'website' => ['nullable', 'size:0'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'patient_name' => 'name',
            'preferred_date' => 'preferred date',
            'preferred_time' => 'preferred time',
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'preferred_date.after_or_equal' => 'Please choose today or a later date.',
            'website.size' => 'Your request could not be sent. Please try again.',
        ];
    }
}
