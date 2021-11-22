<?php

return [
    'lmc_cors' => [
        'allowed_origins' => [
            'https://api.eurekaclusters.eu'
        ],
        'allowed_methods' => ['GET', 'OPTIONS', 'PATCH', 'POST', 'PUT'],
        'allowed_headers' => ['Authorization', 'Content-Type', 'Accept'],
    ],
];
