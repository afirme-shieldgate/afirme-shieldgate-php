<?php

namespace Payment;

use Payment\Resources\{Card, Cash, Charge};

class Settings
{
    const DEFAULT_SECONDS_TIMEOUT = 90;
    const CCAPI = 'ccapi';
    const NOCCAPI = 'noccapi';
    const API_VERSION = "v2";
    const DOMAIN = 'shieldgate.mx';

    const BASE_URL = [
        self::CCAPI => [
            'production' => "https://ccapi." . self::DOMAIN,
            'staging' => "https://ccapi-stg." . self::DOMAIN,
        ],
        self::NOCCAPI => [
            'production' => "https://noccapi-prod." . self::DOMAIN,
            'staging' => "https://noccapi-stg." . self::DOMAIN,
        ]
    ];

    const API_RESOURCES = [
        'card' => [
            'class' => Card::class,
            'api' => self::CCAPI
        ],
        'cash' => [
            'class' => Cash::class,
            'api' => self::NOCCAPI
        ],
        'charge' => [
            'class' => Charge::class,
            'api' => self::CCAPI
        ]
    ];

    const DEFAULT_HEADERS = [
        'Content-Type' => "application/json"
    ];
}
