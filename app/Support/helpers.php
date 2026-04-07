<?php

use App\Models\ExchangeRate;

if (! function_exists('display_currency')) {
    function display_currency(): string
    {
        return session('display_currency', 'SYP');
    }
}

if (! function_exists('currency_symbol')) {
    function currency_symbol(?string $currency): string
    {
        return match ($currency) {
            'USD' => 'دولار',
            'SYP' => 'ل.س',
            default => (string) $currency,
        };
    }
}

if (! function_exists('get_exchange_rate')) {
    function get_exchange_rate(string $fromCurrency, string $toCurrency): float
    {
        if ($fromCurrency === $toCurrency) {
            return 1;
        }

        $direct = ExchangeRate::query()
            ->where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->where('is_active', true)
            ->value('rate');

        if ($direct !== null) {
            return (float) $direct;
        }

        $reverse = ExchangeRate::query()
            ->where('from_currency', $toCurrency)
            ->where('to_currency', $fromCurrency)
            ->where('is_active', true)
            ->value('rate');

        if ($reverse !== null && (float) $reverse > 0) {
            return 1 / (float) $reverse;
        }

        return 1;
    }
}

if (! function_exists('convert_price')) {
    function convert_price($amount, ?string $fromCurrency, ?string $toCurrency = null): float
    {
        $fromCurrency = $fromCurrency ?: 'SYP';
        $toCurrency = $toCurrency ?: display_currency();

        $rate = get_exchange_rate($fromCurrency, $toCurrency);

        return round(((float) $amount) * $rate, 2);
    }
}

if (! function_exists('format_money')) {
    function format_money($amount, ?string $currency = null): string
    {
        $currency = $currency ?: display_currency();

        return number_format((float) $amount, 2) . ' ' . currency_symbol($currency);
    }
}

if (! function_exists('display_price')) {
    function display_price($amount, ?string $fromCurrency = 'SYP'): string
    {
        $targetCurrency = display_currency();
        $converted = convert_price($amount, $fromCurrency, $targetCurrency);

        return format_money($converted, $targetCurrency);
    }
}