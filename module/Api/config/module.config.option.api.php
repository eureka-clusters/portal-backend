<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

$options = [
    'access_token_lifetime'  => 3600,       // 1 hour
    'refresh_token_lifetime' => 1209600,    // 14 days
    'authorization_code_lifetime' => 300,   // 5 minutes
];

return [
    'api_options' => $options,
];
