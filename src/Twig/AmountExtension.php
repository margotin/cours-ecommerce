<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AmountExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('amount', [$this, 'amount'])
        ];
    }

    public function amount(int $value, string $symbol = '€', string $decSep = ',', string $thousandSep = ' '): string
    {
        $finalValue = $value / 100;
        $finalValue = number_format($finalValue, 2, $decSep, $thousandSep);
        return sprintf("%s %s", $finalValue, $symbol);
    }
}
