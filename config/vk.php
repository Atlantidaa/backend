<?php

return [
    'login' => env('VK_API_LOGIN'),
    'password' => env('VK_API_PASSWORD'),

    'base_uri' => 'https://api.vk.com',
    'routes' => [
        'search' => '/method/audio.search',
        'hints' => 'https://vk.com/hints.php',
    ]
];
