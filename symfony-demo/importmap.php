<?php

declare(strict_types=1);
return [
    'app' => [
        'path' => 'app.js',
        'entrypoint' => true,
    ],
    '@symfony/stimulus-bundle' => [
        'path' => '@symfony/stimulus-bundle/loader.js',
    ],
    '@hotwired/stimulus' => [
        'version' => '3.2.2',
    ],
    'typed.js' => [
        'version' => '2.1.0',
    ],
    '@simplewebauthn/browser' => [
        'version' => '13.2.2',
    ],
    '@web-auth/webauthn-stimulus' => [
        'version' => '5.3.7',
    ],
    '@fontsource-variable/inter' => [
        'version' => '5.0.15',
    ],
    '@fontsource-variable/inter/index.min.css' => [
        'version' => '5.0.15',
        'type' => 'css',
    ],
    'material-icons' => [
        'version' => '1.13.12',
    ],
    'material-icons/iconfont/material-icons.min.css' => [
        'version' => '1.13.12',
        'type' => 'css',
    ],
];
