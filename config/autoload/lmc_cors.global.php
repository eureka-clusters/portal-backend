<?php

return [
    'lmc_cors' => [
        /**
         * Set the list of allowed origins domain with protocol.
         */
        'allowed_origins' => [
            'https://tool.eureka-clusters-ai.eu'
        ],

        /**
         * Set the list of HTTP verbs.
         */
        'allowed_methods' => ['GET', 'OPTIONS', 'PATCH', 'POST', 'PUT'],

        /**
         * Set the list of headers. This is returned in the preflight request to indicate
         * which HTTP headers can be used when making the actual request
         */
        'allowed_headers' => ['Authorization', 'Content-Type', 'Accept'],
    ],
];
