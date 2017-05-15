<?php
use Lapaz\Amechan\AssetManager;

$am = new AssetManager();

$am->asset('jquery', [
    'file' => '/assets/vendor/jquery/dist/jquery.js',
    'section' => 'js',
]);

$am->asset('bootstrap', [
    'baseUrl' => '/assets/vendor/bootstrap/dist',
    'bundles' => [
        [
            'files' => ['css/bootstrap.css', 'css/bootstrap-theme.css'],
            'section' => 'css',
        ],
        [
            'file' => 'js/bootstrap.js',
            'section' => 'js',
        ],
    ],
    'dependency' => 'jquery',
]);

// URLs are mapped when compiled version assets exist.
if (is_dir(__DIR__ . '/../web/assets/dist')) {
    $am->map('/assets', [
        'dist/css/all.min.css' => [
            'vendor/bootstrap/dist/css/bootstrap.css',
            'vendor/bootstrap/dist/css/bootstrap-theme.css',
        ],
        'dist/js/all.min.js' => [
            'vendor/jquery/dist/jquery.js',
            'vendor/bootstrap/dist/js/bootstrap.js',
        ],
    ]);

    // URLs are replaced to revision hashed version.
    if (is_file(__DIR__ . '/../web/assets/dist/rev-manifest.json')) {
        $am->rev('/assets/dist/', json_decode(file_get_contents(
            __DIR__ . '/../web/assets/dist/rev-manifest.json'
        ), true));
    }
}

return $am;
