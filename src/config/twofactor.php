<?php

return [
    'routes' => [
        'redirect_after_verify2fa' => 'dashboard', // Redirecionar após verificar o 2FA
        'middleware_sanctum' => 'auth:sanctum'
    ],
];
