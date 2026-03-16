<?php

return [

    'tax_rate' => (float) env('ORDER_TAX_RATE', 0),

    'currency' => env('WALLET_CURRENCY', 'USD'),

    'loyalty' => [
        'points_per_dollar' => (float) env('LOYALTY_POINTS_PER_DOLLAR', 10),
        'points_per_currency_unit' => (int) env('LOYALTY_POINTS_PER_CURRENCY_UNIT', 100),
    ],

];
