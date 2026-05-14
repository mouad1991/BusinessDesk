<?php

namespace App\Services;

class AmountInWordsService
{
    private array $ones = [
        '', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf',
        'dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize',
        'dix-sept', 'dix-huit', 'dix-neuf',
    ];

    private array $tens = [
        '', '', 'vingt', 'trente', 'quarante', 'cinquante',
        'soixante', 'soixante', 'quatre-vingt', 'quatre-vingt',
    ];

    public function convert(float $amount): string
    {
        if ($amount < 0) {
            return 'moins ' . $this->convert(-$amount);
        }

        $amount = round($amount, 2);
        $intPart = (int) $amount;
        $decPart = (int) round(($amount - $intPart) * 100);

        $result = $this->convertInteger($intPart);
        $result .= $intPart > 1 ? ' dirhams' : ' dirham';

        if ($decPart > 0) {
            $result .= ' ' . $this->convertInteger($decPart);
            $result .= $decPart > 1 ? ' centimes' : ' centime';
        }

        return ucfirst($result);
    }

    private function convertInteger(int $n): string
    {
        if ($n === 0) return 'zéro';
        if ($n < 0) return 'moins ' . $this->convertInteger(-$n);

        $result = '';

        if ($n >= 1000000000) {
            $billions = (int) ($n / 1000000000);
            $result .= $this->convertInteger($billions) . ' milliard' . ($billions > 1 ? 's' : '');
            $n %= 1000000000;
            if ($n > 0) $result .= ' ';
        }

        if ($n >= 1000000) {
            $millions = (int) ($n / 1000000);
            $result .= $this->convertInteger($millions) . ' million' . ($millions > 1 ? 's' : '');
            $n %= 1000000;
            if ($n > 0) $result .= ' ';
        }

        if ($n >= 1000) {
            $thousands = (int) ($n / 1000);
            if ($thousands === 1) {
                $result .= 'mille';
            } else {
                $result .= $this->convertInteger($thousands) . ' mille';
            }
            $n %= 1000;
            if ($n > 0) $result .= ' ';
        }

        if ($n >= 100) {
            $hundreds = (int) ($n / 100);
            if ($hundreds === 1) {
                $result .= 'cent';
            } else {
                $result .= $this->ones[$hundreds] . ' cent';
                if ($n % 100 === 0) $result .= 's';
            }
            $n %= 100;
            if ($n > 0) $result .= ' ';
        }

        if ($n > 0) {
            $result .= $this->convertBelowHundred($n);
        }

        return trim($result);
    }

    private function convertBelowHundred(int $n): string
    {
        if ($n < 20) {
            return $this->ones[$n];
        }

        $tensDigit = (int) ($n / 10);
        $onesDigit = $n % 10;

        // Special cases for French: 70-79 and 90-99
        if ($tensDigit === 7) {
            // soixante-dix, soixante-onze...
            return 'soixante-' . $this->ones[10 + $onesDigit];
        }

        if ($tensDigit === 9) {
            // quatre-vingt-dix, quatre-vingt-onze...
            return 'quatre-vingt-' . $this->ones[10 + $onesDigit];
        }

        $tensWord = $this->tens[$tensDigit];

        if ($onesDigit === 0) {
            // quatre-vingts (plural) but vingt in compounds
            return $tensDigit === 8 ? 'quatre-vingts' : $tensWord;
        }

        if ($onesDigit === 1 && $tensDigit !== 8) {
            return $tensWord . ' et un';
        }

        return $tensWord . '-' . $this->ones[$onesDigit];
    }
}
