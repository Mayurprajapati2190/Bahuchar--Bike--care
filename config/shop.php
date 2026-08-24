<?php

return [

    'name' => env('SHOP_NAME') ?: 'Bahuchar Bike Care',

    'address' => env('SHOP_ADDRESS') ?: 'Shop No. 7, Bahuchar Auto Care, near Vishwas City 3, Vandematram, Gota, Ahmedabad - 382481',

    'phone' => env('SHOP_PHONE') ?: '9824799203',

    'gstin' => env('SHOP_GSTIN', ''),

    'bill_prefix' => env('SHOP_BILL_PREFIX', 'BBC'),

    'tagline' => env('SHOP_TAGLINE') ?: 'Trusted bike service & care in your neighbourhood',

    'hours' => env('SHOP_HOURS') ?: 'Mon – Sat: 8:30 AM – 8:30 PM | Sunday: 8:30 AM – 3:00 PM',

];
