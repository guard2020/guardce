<?php

/*
    |--------------------------------------------------------------------------
    | Security Dashboard - User Defined Variables
    |--------------------------------------------------------------------------
    |
    | This is a set of variables that are made specific to this application
    | that are better placed here rather than in .env file.
    | Use config('your_key') to get the values.
    |
    */
    return [
        'cb_api' => env('CB_API'),
        'kafka_path' => env('KAFKA_PATH'),
        'kafka_url' => env('KAFK_URL'),
        'sc_api' => env('SC_API'),
        'kibana_url' => env('KIBANA_URL')
    ];