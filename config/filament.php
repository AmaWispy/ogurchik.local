<?php

return [
    'default_locale' => 'ru',
    
    'layout' => [
        'forms' => [
            'actions' => [
                'alignment' => 'left',
            ],
        ],
    ],
    
    'auth' => [
        'guard' => 'web',
        'pages' => [
            'login' => \Filament\Pages\Auth\Login::class,
        ],
    ],
    
    'notifications' => [
        'duration' => 5000,
    ],
    
    'broadcasting' => [
        'echo' => [
            'broadcaster' => 'pusher',
            'key' => env('VITE_PUSHER_APP_KEY'),
            'cluster' => env('VITE_PUSHER_APP_CLUSTER'),
            'forceTLS' => true,
        ],
    ],
]; 