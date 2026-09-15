<?php

use Morilog\Jalali\Jalalian;

if (! function_exists('shamsi')) {
    /**
     * Render any Carbon-ish date as an Afghan Shamsi (Jalali) string — the
     * calendar every screen is supposed to show per the original plan.
     * Every list/detail view should call this instead of ->format() on the
     * raw Gregorian Carbon instance.
     *
     * @param  \Carbon\Carbon|\Illuminate\Support\Carbon|string|null  $date
     * @param  string  $format  Jalalian tokens (Y/m/d numeric, or 'd F Y' for the Persian month name)
     */
    function shamsi($date, string $format = 'Y/m/d'): string
    {
        if (! $date) {
            return '';
        }

        if (is_string($date)) {
            $date = \Illuminate\Support\Carbon::parse($date);
        }

        return Jalalian::fromCarbon($date)->format($format);
    }
}
