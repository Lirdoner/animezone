<?php $view->extend('layout') ?>

<div class="bs-callout bs-callout-info">
    <form class="form-horizontal" role="form" method="post" action="<?= $action ?>">
        <p><?= $msg ?></p>
        <div class="text-center">
            <input type="hidden" name="confirm" value="true">
            <button type="submit" class="btn btn-success"><i class="fa fa-thumbs-o-up"></i> Tak</button>
            <a href="#" onclick="window.history.back();return false;" class="btn btn-danger"><i class="fa fa-thumbs-o-down"></i> Nie</a>
        </div>
    </form>
</div>