<?php

return [
    'default_app_name' => 'common',
    'apps' => [
        'common' => [
            'webhook_secret' => env('WHATSAPP_COMMON_APP_WEBHOOK_SECRET', '01dfb5bb-ce14-4fe7-8507-c23c6d178256'),
            'api_key' => env('WHATSAPP_COMMON_APP_API_KEY', ''),
            'number_id' => env('WHATSAPP_COMMON_APP_NUMBER_ID', ''),
        ],
    ],
];
