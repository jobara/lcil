<?php

dataset('emailRestrictions', function () {
    $pass = true;
    $fail = false;

    return [
        'allow email when restrictions disabled' => [
            [
                'settings.registration.restricted' => false,
                'settings.registration.allowlist' => [],
                'settings.registration.blocklist' => ['example.com'],
            ],
            $pass,
        ],
        'allow email when allowlist null and blocklist empty' => [
            [
                'settings.registration.restricted' => true,
                'settings.registration.allowlist' => null,
                'settings.registration.blocklist' => [],
            ],
            $pass,
        ],
        'allow email when allowlist empty and blocklist empty' => [
            [
                'settings.registration.restricted' => true,
                'settings.registration.allowlist' => [],
                'settings.registration.blocklist' => [],
            ],
            $pass,
        ],
        'block email when not in allowlist' => [
            [
                'settings.registration.restricted' => true,
                'settings.registration.allowlist' => ['testing.ca'],
                'settings.registration.blocklist' => [],
            ],
            $fail,
        ],
        'block email when in blocklist' => [
            [
                'settings.registration.restricted' => true,
                'settings.registration.allowlist' => [],
                'settings.registration.blocklist' => ['example.com'],
            ],
            $fail,
        ],
        'block email when in allowlist and blocklist' => [
            [
                'settings.registration.restricted' => true,
                'settings.registration.allowlist' => ['example.com'],
                'settings.registration.blocklist' => ['example.com'],
            ],
            $fail,
        ],
    ];
});
