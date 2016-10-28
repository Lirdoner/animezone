<?php $view->extend('layout') ?>

<div class="site-main">
    <?php $view['slots']->output('_content') ?>
</div>
<div class="site-sidebar">
    <?php $view['slots']->output('_sidebar') ?>
</div>