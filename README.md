# Amechan : Asset Manager Essentials Chain

Amechan (means candy drop in Osaka, Japan) is a lightweight pre-processed asset link manager for PHP.
This library has no side effects unlink Assetic, Sprockets Rails or such as, instead it works better with NodeJS tools like Gulp.

## Quick Start

Define named asset which bundles JS or CSS contents:

```php
$assetManagr->asset('jquery', [ ... ]);
$assetManagr->asset('some-jquery-plugin', [ ... ]);
$assetManagr->asset('and-another-one', [ ... ]);
```

In your view context, require assets:

```php
$assets = $assetManagr->newCollection();

$assets->add('some-jquery-plugin');
$assets->add('and-another-one');
```

Then, in head or end of body tags (often included in the layout template):

```php
foreach ($assets->collectUrls('css') as $url) {
    echo "<link href="{$url}" rel="stylesheet">\n";
}

foreach ($assets->collectUrls('js') as $url) {
    echo "<script src="{$url}"></script>\n";
}
```

All required assets are collected and expanded there.

## Features

- Bundling multi sectioned assets as single unit
- Link order auto detection according to dependency chain
- Source to built URL mapping (any to `*.min.js`)
- Revision hash support with `rev-manifest.json` format

## Definition

// TBD (See `/example` project)
