<?php
/** @var \League\Plates\Template\Template|IdeAssetSupportHelperInterface $this */

$this->layout('_layout', [
    'title' => 'Index Page',
]);

$this->assets()->add('bootstrap');
?>
<p>Index page using Bootstrap <span class="glyphicon glyphicon-heart"></span></p>
<p>CSS was loaded from <?= $this->assetUrl('/assets/vendor/bootstrap/dist/css/bootstrap.css'); ?>.</p>

<div><a href="/no-bootstrap-page">Go to No-Bootstrap page</a></div>
