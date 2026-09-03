{{--
    The full week of opening hours, with today's row marked.

    scheduleRows() always returns all seven days in order, so this table never
    has gaps even if the saved JSON is incomplete.

    Today used to be a filled teal band across the row. A block of colour in a
    list of times is louder than the information deserves — the row is now
    marked with a rule down its left edge and a weight change, which finds the
    eye just as fast without shouting.
--}}

@php
    use Illuminate\Support\Carbon;

    $rows = $doctor->scheduleRows();
@endphp

<table {{ $attributes->class('w-full text-left') }}>
    <caption class="sr-only">Opening hours for {{ $doctor->name }}</caption>
    <thead class="sr-only">
        <tr>
            <th scope="col">Day</th>
            <th scope="col">Hours</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-line">
        @foreach ($rows as $row)
            <tr @class(['relative', 'text-ink' => $row['is_today']])>
                <th scope="row"
                    @class([
                        'py-3.5 pr-4 text-sm',
                        'border-l-2 border-accent pl-3 font-semibold text-ink' => $row['is_today'],
                        'pl-[calc(0.75rem+2px)] font-normal text-muted' => ! $row['is_today'],
                    ])>
                    {{ $row['label'] }}
                    @if ($row['is_today'])
                        <span class="ml-2 align-middle text-[10px] font-semibold uppercase tracking-[0.14em] text-accent">
                            Today
                        </span>
                    @endif
                </th>

                <td @class([
                        'py-3.5 pr-3 text-right text-sm tabular-nums',
                        'font-medium text-ink' => $row['is_today'] && ! $row['is_closed'],
                        'text-muted' => ! ($row['is_today'] && ! $row['is_closed']),
                    ])>
                    @if ($row['is_closed'] || blank($row['opens']) || blank($row['closes']))
                        Closed
                    @else
                        {{ Carbon::parse($row['opens'])->format('g:i A') }}
                        <span class="text-line-strong" aria-hidden="true">&ndash;</span>
                        {{ Carbon::parse($row['closes'])->format('g:i A') }}
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
