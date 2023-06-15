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
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook' => [
            'secret' => env('STRIPE_WEBHOOK_SECRET'),
            'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
        ],
    ],

    'twitter' => [
        'consumer_key'          =>  env('TWITTER_CONSUMER_KEY'          ,   ''                         ),
        'consumer_secret'       =>  env('TWITTER_CONSUMER_SECRET'       ,   ''),
        'access_token'          =>  env('TWITTER_ACCESS_TOKEN'          ,   ''),
        'access_token_secret'   =>  env('TWITTER_ACCESS_TOKEN_SECRET'   ,   ''     ),
        'publish_to'            =>  env('TWITTER_PUBLISH_TO'            ,                                                      ),
    ],

    'facebook' => [
        'page_id'    =>  env('FACEBOOK_PAGE_ID', ''),
        'page_access_token' =>  env('FACEBOOK_PAGE_ACCESS_TOKEN', ''),
    ],

    'sns' => [
        'twitter'       =>  env('SNS_TWITTER_URL'       ,   ''                                       ),
        'facebook'      =>  env('SNS_FACEBOOK_URL'      ,   ''                   ),
    ],

];
