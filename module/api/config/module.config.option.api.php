<?php

declare(strict_types=1);

$options = [
    'access_token_lifetime'       => 3600, // 1 hour
    'refresh_token_lifetime'      => 1209600, // 14 days
    'authorization_code_lifetime' => 300, // 5 minutes
];

return [
    'api_options' => $options,
];
