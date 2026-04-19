<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Restaurant Information
    |--------------------------------------------------------------------------
    |
    | This file is for storing basic information about the restaurant
    | such as name, address, contact information, and hours.
    |
    */

    'name' => env('RESTAURANT_NAME', 'The District Tapas Bar & Restaurant'),
    
    'address' => env('RESTAURANT_ADDRESS', '202 King St. East, Hamilton, ON L8N 1B5'),
    
    'phone' => env('RESTAURANT_PHONE', '(905) 525-0779'),
    
    'email' => env('RESTAURANT_EMAIL', 'info@district-tapas.com'),
    
    'website' => env('RESTAURANT_WEBSITE', 'https://district-tapas.com'),
    
    'timezone' => 'America/Toronto',

    'hours' => [
        'sunday'    => ['open' => '17:00', 'close' => '21:00'],
        'monday'    => ['closed' => true],
        'tuesday'   => ['open' => '17:00', 'close' => '21:00'],
        'wednesday' => ['open' => '17:00', 'close' => '21:00'],
        'thursday'  => ['open' => '17:00', 'close' => '21:00'],
        'friday'    => ['open' => '17:00', 'close' => '22:00'],
        'saturday'  => ['open' => '17:00', 'close' => '22:00'],
    ],
    
    'social_media' => [
        'facebook' => env('RESTAURANT_FACEBOOK', 'https://facebook.com/districttapas'),
        'instagram' => env('RESTAURANT_INSTAGRAM', 'https://instagram.com/districttapas'),
        'twitter' => env('RESTAURANT_TWITTER', 'https://twitter.com/districttapas'),
    ],
    
    'order_notification_emails' => [
        env('ADMIN_EMAIL', 'admin@district-tapas.com'),
    ],

]; 