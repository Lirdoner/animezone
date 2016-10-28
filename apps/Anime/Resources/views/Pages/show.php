<?php $view->extend('content') ?>

<?php $view['slots']->set('_title', $page->getName().' - '.$app->getConfig()->anime->get('title')) ?>
<?php $view['slots']->set('_sidebar', $view->render('Sidebar/faq', array('sidebar' => $sidebar))) ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="glyphicon glyphicon-file"></i> <?= $page->getName() ?></h3>
    </div>
    <div class="panel-body">
        <p>
            <?= $page->getContent() ?>
        </p>
    </div>
</div>