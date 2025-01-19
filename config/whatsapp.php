<?php

return [
    'default_app' => 'common',
    'apps' => [
        'common' => [
            'webhook_secret' => env('WHATSAPP_COMMON_APP_WEBHOOK_SECRET', '01dfb5bb-ce14-4fe7-8507-c23c6d178256'),
            'api_key' => env('WHATSAPP_COMMON_APP_API_KEY', ''),
        ],
    ],
];
