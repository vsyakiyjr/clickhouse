<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID', '306360763868012'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET', 'dd4c398d5d5f0f8659803e717a14c4c6'),
        'redirect' => '/socialite/facebook',
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID', '369973888555-s2c0qcuah44ab9ea0u66p86br88eg3n0.apps.googleusercontent.com'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET', '9_yedtKxkAwsBwNSnqbHn1ly'),
        'redirect' => '/socialite/google',
    ],

    'vkontakte' => [
        'client_id' => env('VK_CLIENT_ID'),
        'client_secret' => env('VK_CLIENT_SECRET'),
        'redirect' => '/socialite/vkontakte',
    ],
];
