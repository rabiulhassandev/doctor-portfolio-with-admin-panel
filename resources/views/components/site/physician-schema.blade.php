{{--
    schema.org structured data describing the practice.

    Google reads this to build a rich result: the doctor's name, specialism,
    address, phone, opening hours and map pin. Included on the About and Contact
    pages, where that information genuinely belongs.

    Test the output with Google's Rich Results Test after a rebrand.
--}}

@php
    use App\Support\Media;
    use Illuminate\Support\Carbon;

    // Physician is a subtype of MedicalBusiness, which is itself a LocalBusiness,
    // so this one block satisfies both the Physician and LocalBusiness cases.
    $schema = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'Physician',
        'name' => $doctor->name,
        'medicalSpecialty' => $doctor->specialization,
        'description' => $doctor->short_bio ?: $doctor->meta_description,
        'url' => route('home'),
        'image' => Media::absoluteUrl($doctor->photo),
        'telephone' => $doctor->phone,
        'email' => $doctor->email,
    ]);

    if (filled($doctor->address_line) || filled($doctor->city)) {
        $schema['address'] = array_filter([
            '@type' => 'PostalAddress',
            'streetAddress' => $doctor->address_line,
            'addressLocality' => $doctor->city,
            'addressRegion' => $doctor->state,
            'postalCode' => $doctor->postal_code,
            'addressCountry' => $doctor->country,
        ]);
    }

    if ($doctor->map_latitude && $doctor->map_longitude) {
        $schema['geo'] = [
            '@type' => 'GeoCoordinates',
            'latitude' => $doctor->map_latitude,
            'longitude' => $doctor->map_longitude,
        ];
    }

    // schema.org wants opening hours as "Mo 09:00-17:00" style entries.
    $openingHours = $doctor->scheduleRows()
        ->reject(fn ($row) => $row['is_closed'] || blank($row['opens']) || blank($row['closes']))
        ->map(fn ($row) => [
            '@type' => 'OpeningHoursSpecification',
            'dayOfWeek' => 'https://schema.org/'.ucfirst($row['day']),
            'opens' => Carbon::parse($row['opens'])->format('H:i'),
            'closes' => Carbon::parse($row['closes'])->format('H:i'),
        ])
        ->values()
        ->all();

    if ($openingHours) {
        $schema['openingHoursSpecification'] = $openingHours;
    }

    $socials = $doctor->activeSocialLinks();

    if ($socials->isNotEmpty()) {
        $schema['sameAs'] = $socials->values()->all();
    }
@endphp

<script type="application/ld+json">
    {!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
