<?php

return [
    'lmc_cors' => [
        'allowed_origins' => [
            'https://eurekaclusters.eu',
//            'http://localhost:5173',
        ],
        'allowed_methods' => ['GET', 'OPTIONS', 'PATCH', 'POST', 'PUT'],
        'allowed_headers' => ['Authorization', 'Content-Type', 'Accept'],
    ],
];
