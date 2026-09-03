<?php

use App\Models\AppointmentRequest;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Appointment *requests* submitted from the contact page.
 *
 * This is deliberately request-based, not real-time slot booking: the patient
 * states a preferred date/time and the practice confirms or rejects it by hand.
 * (Live availability + payments belong to the Pro tier.)
 *
 * @see AppointmentRequest
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointment_requests', function (Blueprint $table) {
            $table->id();

            $table->string('patient_name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->date('preferred_date');
            $table->string('preferred_time');             // A slot label, e.g. "Morning (9am – 12pm)".
            $table->text('message')->nullable();

            /**
             * Values come from \App\Enums\AppointmentStatus. Stored as a string
             * rather than a native ENUM so adding a status later (e.g. "completed"
             * in the Pro tier) needs no destructive column change.
             */
            $table->string('status')->default('pending')->index();

            $table->text('admin_notes')->nullable();      // Internal-only; never shown publicly.
            $table->timestamp('responded_at')->nullable();

            $table->timestamps();

            // The admin list is sorted newest-first and filtered by status.
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_requests');
    }
};
