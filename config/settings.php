<?php

use Carbon\Carbon;

return [

    'year' => [
        'min' => env('SETTINGS_YEAR_MIN', 1800),
        'max' => env('SETTINGS_YEAR_MAX', Carbon::now()->format('Y') + env('SETTINGS_YEAR_MAX_INCREMENT', 8)),
    ],

];
