<?php

return [
    // How long an issued bearer token stays valid, in minutes.
    'ttl_minutes' => env('AUTH_TOKEN_TTL_MINUTES', 43200), // default 30 days

    // Maximum accepted upload size for images, in kilobytes.
    'max_image_size_kb' => env('MAX_IMAGE_SIZE_KB', 5120), // default 5 MB
];
