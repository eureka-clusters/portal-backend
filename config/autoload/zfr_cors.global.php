<?php
/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

return [
    'zfr_cors' => [
        /**
         * Set the list of allowed origins domain with protocol.
         */
        'allowed_origins' => [
            'https://tool.eureka-clusters-ai.eu',
            'http://localhost:3000',
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