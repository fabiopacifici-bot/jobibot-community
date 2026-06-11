<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Provider Configuration
    |--------------------------------------------------------------------------
    |
    | Supported: "openai", "ollama", "privateai"
    |
    | Each provider has its own config section below.
    | The 'provider' key selects which one is active.
    |
    */
    'provider' => env('JOBIBOT_AI_PROVIDER', 'openai'),

    'model'       => env('JOBIBOT_AI_MODEL', 'gpt-4o'),
    'temperature' => (float) env('JOBIBOT_AI_TEMPERATURE', 0.6),

    /*
    |--------------------------------------------------------------------------
    | Provider-specific settings
    |--------------------------------------------------------------------------
    */
    'providers' => [
        'openai' => [
            'api_key' => env('OPENAI_API_KEY', ''),
            'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
            'model'    => env('OPENAI_MODEL', 'gpt-4o'),
        ],

        'ollama' => [
            'base_url' => env('OLLAMA_BASE_URL', 'http://localhost:11434'),
            'model'    => env('OLLAMA_MODEL', 'gemma3'),
        ],

        'privateai' => [
            'base_url' => env('PRIVATEAI_BASE_URL', 'http://localhost:8005'),
            'model'    => env('PRIVATEAI_MODEL', 'qwen3-7b'),
            'api_key'  => env('PRIVATEAI_API_KEY', null),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Job Search
    |--------------------------------------------------------------------------
    */
    'remotive_api_url' => env('REMOTIVE_API_URL', 'https://remotive.com/api/remote-jobs'),

    /*
    |--------------------------------------------------------------------------
    | Storage
    |--------------------------------------------------------------------------
    */
    'cv_disk'       => env('JOBIBOT_CV_DISK', 'local'),
    'cv_max_size_kb' => (int) env('JOBIBOT_CV_MAX_SIZE_KB', 5120),
];