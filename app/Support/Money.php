<?php

namespace App\Support;

use NumberFormatter;

/**
 * Para her yerde KURUŞ cinsinden tam sayı taşınır; biçimlendirme yalnızca
 * görüntülemede yapılır. Kayan noktalı sayı hesaba hiç girmez.
 */
class Money
{
    public static function format(int $amount, ?string $currency = null): string
    {
        $currency ??= config('commerce.currency');

        $formatter = new NumberFormatter(app()->getLocale().'_CY', NumberFormatter::CURRENCY);
        $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $amount % 100 === 0 ? 0 : 2);

        return $formatter->formatCurrency($amount / 100, $currency);
    }
}
