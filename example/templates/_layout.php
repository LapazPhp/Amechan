<?php
/** @var \League\Plates\Template\Template|IdeAssetSupportHelperInterface $this */
/** @var string $title */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $this->e($title) ?></title>
    <?php foreach ($this->assets()->collectUrls('css') as $url): ?>
        <link href="<?= $url ?>" rel="stylesheet">
    <?php endforeach; ?>
</head>
<body>
<div class="container">
    <header>
        <h1>Welcome to Lapaz\Amechan example</h1>
    </header>

    <?= $this->section('content') ?>

    <hr>
    <footer>
        <small>&copy; My awesome company co.ltd,</small>
    </footer>
</div>

<?php foreach ($this->assets()->collectUrls('js') as $url): ?>
    <script src="<?= $url ?>"></script>
<?php endforeach; ?>
<?= $this->section('script') ?>
</body>
</html>
