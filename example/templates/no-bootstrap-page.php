<?php
/** @var \League\Plates\Template\Template|IdeAssetSupportHelperInterface $this */

$this->layout('_layout', [
    'title' => 'No Bootstrap',
]);

// DISABLE: $assets->add('bootstrap');
?>
<p class="no-bootstrap">No Bootstrap page</p>
<div><a href="/">Return to index</a></div>

<?php $this->assets()->add('jquery'); ?>
<?php $this->push('script'); ?>
<script>
    $(function () {
        $('.no-bootstrap').append(', but at least jQuery available');
    });
</script>
<?php $this->end(); ?>
