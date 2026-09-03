<?php

use App\Models\DoctorProfile;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Two facts a Bangladeshi practice is expected to state, and which the original
 * schema had nowhere to put.
 *
 * A patient here checks the BMDC registration number the way a British patient
 * checks the GMC number — it is the difference between a qualified consultant
 * and the man in the pharmacy who is also called "doctor". Chambers are also
 * named separately from the doctor: the sign outside says "Sohrid Heart Care",
 * and that is the name a patient repeats to a rickshaw driver.
 *
 * Both are nullable, so a practice in a market that uses neither simply leaves
 * them blank and nothing renders.
 *
 * @see DoctorProfile
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctor_profiles', function (Blueprint $table) {
            $table->string('registration_label')->nullable()->after('specialization');
            $table->string('registration_number')->nullable()->after('registration_label');
            $table->string('chamber_name')->nullable()->after('registration_number');
        });
    }

    public function down(): void
    {
        Schema::table('doctor_profiles', function (Blueprint $table) {
            $table->dropColumn(['registration_label', 'registration_number', 'chamber_name']);
        });
    }
};
