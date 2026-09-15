<?php

namespace Tests\Unit;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

/**
 * Every view in the app is supposed to show the Afghan Shamsi calendar, per
 * the original plan — this pins the shamsi() helper's actual conversion so
 * a regression here (e.g. an off-by-one in the Jalali library, or someone
 * reverting to raw ->format() on a Gregorian Carbon) fails loudly.
 */
class ShamsiHelperTest extends TestCase
{
    public function test_converts_a_known_gregorian_date_to_its_known_shamsi_equivalent(): void
    {
        // 2026-09-15 (Gregorian) is 1405-06-24 in the Shamsi calendar.
        $gregorian = Carbon::create(2026, 9, 15);

        $this->assertSame('1405/06/24', shamsi($gregorian));
    }

    public function test_supports_the_persian_month_name_format(): void
    {
        $gregorian = Carbon::create(2026, 9, 15);

        $this->assertSame('24 شهریور 1405', shamsi($gregorian, 'd F Y'));
    }

    public function test_accepts_a_date_string_directly(): void
    {
        $this->assertSame('1405/06/24', shamsi('2026-09-15'));
    }

    public function test_returns_empty_string_for_null(): void
    {
        $this->assertSame('', shamsi(null));
    }
}
