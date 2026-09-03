<?php

namespace App\Filament\Widgets;

use App\Enums\AppointmentStatus;
use App\Models\AppointmentRequest;
use Filament\Widgets\ChartWidget;

/**
 * Enquiries per week for the last twelve weeks, split by outcome.
 *
 * The stat cards above answer "what is waiting for me now?". This answers the
 * question the doctor asks at the end of the month instead: is the website
 * actually bringing patients in, and are we replying to them?
 *
 * Deliberately a stacked bar rather than a line — twelve weeks of small
 * integers looks like noise as a line, and the split by status is the point.
 */
class RequestsPerWeek extends ChartWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Enquiries per week';

    protected ?string $description = 'The last twelve weeks, by outcome.';

    protected int|string|array $columnSpan = 2;

    protected ?string $maxHeight = '280px';

    /** Weeks of history to plot. */
    private const WEEKS = 12;

    protected function getData(): array
    {
        $start = now()->startOfWeek()->subWeeks(self::WEEKS - 1);

        /*
         | One grouped query rather than one per week: `YYYY-WW` buckets are
         | built in PHP from the row's created_at so this stays portable across
         | MySQL, Postgres and the SQLite used in tests, where the week-number
         | functions all differ.
         */
        $rows = AppointmentRequest::query()
            ->where('created_at', '>=', $start)
            ->get(['created_at', 'status']);

        $buckets = [];

        for ($i = 0; $i < self::WEEKS; $i++) {
            $week = $start->copy()->addWeeks($i);

            $buckets[$week->format('o-W')] = [
                'label' => $week->format('j M'),
                AppointmentStatus::Confirmed->value => 0,
                AppointmentStatus::Pending->value => 0,
                AppointmentStatus::Rejected->value => 0,
            ];
        }

        foreach ($rows as $row) {
            $key = $row->created_at->format('o-W');

            if (isset($buckets[$key])) {
                $buckets[$key][$row->status->value]++;
            }
        }

        $series = fn (AppointmentStatus $status) => array_map(
            fn (array $bucket) => $bucket[$status->value],
            array_values($buckets),
        );

        return [
            'datasets' => [
                [
                    'label' => 'Confirmed',
                    'data' => $series(AppointmentStatus::Confirmed),
                    'backgroundColor' => '#15803d',
                    'borderRadius' => 4,
                ],
                [
                    'label' => 'Waiting',
                    'data' => $series(AppointmentStatus::Pending),
                    'backgroundColor' => '#d97706',
                    'borderRadius' => 4,
                ],
                [
                    'label' => 'Rejected',
                    'data' => $series(AppointmentStatus::Rejected),
                    'backgroundColor' => '#94a3b8',
                    'borderRadius' => 4,
                ],
            ],
            'labels' => array_column(array_values($buckets), 'label'),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'x' => [
                    'stacked' => true,
                    'grid' => ['display' => false],
                ],
                'y' => [
                    'stacked' => true,
                    // Enquiry counts are whole patients; half a patient on the
                    // axis makes the chart look broken on a quiet week.
                    'ticks' => ['precision' => 0],
                    'beginAtZero' => true,
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => ['usePointStyle' => true, 'boxWidth' => 8],
                ],
            ],
        ];
    }

    /** Hide the chart until there is enough history for it to mean anything. */
    public static function canView(): bool
    {
        return AppointmentRequest::query()->exists();
    }
}
