# Amechan : Asset Manager Essentials Chain

[![Build Status](https://travis-ci.org/LapazPhp/Amechan.svg?branch=master)](https://travis-ci.org/LapazPhp/Amechan)

Amechan (means candy drop in Osaka, Japan) is a lightweight pre-processed asset link manager for PHP.
This library has no side effects unlike Assetic, Sprockets Rails or such as, instead it works better with NodeJS tools like Gulp.

## Quick Start

Define named asset which bundles JS or CSS contents:

```php
$assetManager = new AssetManager();

$assetManagr->asset('jquery', [
    'file' => '/assets/vendor/jquery/dist/jquery.js',
    'section' => 'js',
]);
$assetManagr->asset('bootstrap', [
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
$assetManagr->asset('some-jquery-plugin', [ ... ]);
$assetManagr->asset('and-another-one', [ ... ]);
```

Prepare asset collection for presentation session before rendering.

```php
$assets = $assetManagr->newCollection();
```

In your view file, require assets:

`layout.php`

```php
<?php
$assets->add('bootstrap');
?>
<!DOCTYPE html>
<html>
<head>
... layout here
```

`app-view.php`

```php
<?php
$assets->add('bootstrap');
$assets->add('some-jquery-plugin');
?>
<div id="app-view">
    ... app html
</div>
```

Redundant `->add()` calls are summarized and they are ordered by its dependencies.

Then, in head or end of body tags (often included in the layout template):

```php
<?php
foreach ($assets->collectUrls('css') as $url) {
    echo "<link href="{$url}" rel="stylesheet">\n";
}
?>
</head>
```

```php
<?php
foreach ($assets->collectUrls('js') as $url) {
    echo "<script src="{$url}"></script>\n";
}
?>
</body>
```
All required assets are collected and expanded there.

```html
<link href="/assets/vendor/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
<link href="/assets/vendor/bootstrap/dist/css/bootstrap-theme.css" rel="stylesheet">
</head>
<body>
    :
    :
<script src="/assets/vendor/jquery/dist/jquery.js"></script>
<script src="/assets/vendor/bootstrap/dist/js/bootstrap.js"></script>
<script src="/assets/vendor/bootstrap/dist/js/some-jquery-plugin.js"></script>
</body>
```

## Features

- Bundling multi sectioned assets as single unit
- Link order auto detection according to dependency chain
- Source to built URL mapping (any to `*.min.js`)
- Revision hash support with `rev-manifest.json` format


URLs can be mapped when compiled version assets exist.

```php
if (is_dir(__DIR__ . '/../public/assets/dist')) {
    $assetManager->mapping(new UnifiedResourceMapper('/assets', [
        'dist/css/all.min.css' => [
            'vendor/bootstrap/dist/css/bootstrap.css',
            'vendor/bootstrap/dist/css/bootstrap-theme.css',
        ],
        'dist/js/all.min.js' => [
            'vendor/jquery/dist/jquery.js',
            'vendor/bootstrap/dist/js/bootstrap.js',
        ],
    ]));
}
```

```html
<link href="/assets/dist/css/all.min.css" rel="stylesheet">

<script src="/assets/dist/js/all.min.js"></script>
```

URLs can be replaced to revision hashed version.

```php
if (is_file(__DIR__ . '/../web/assets/dist/rev-manifest.json')) {
    $manifest = json_decode(file_get_contents(
        __DIR__ . '/../web/assets/dist/rev-manifest.json'
    ), true);
    $assetManager->mapping(new RevisionHashMapper('/assets/dist/', $manifest));
}
```

```html
<link href="/assets/dist/css/all-33f4c35457.min.css" rel="stylesheet">

<script src="/assets/dist/js/all-5d8020ef9b.min.js"></script>
```

## Definition

// TBD (See `/example` project)
