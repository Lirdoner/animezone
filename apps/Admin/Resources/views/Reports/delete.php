<?php $view->extend('content') ?>

<?php $view['slots']->set('_sidebar', $view->render('Sidebar/users/layout', array('current' => 'comments'))) ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="glyphicon glyphicon-remove-sign"></i> Usuń raport</h3>
        <a href="<?= $app->generateUrl('reports_index') ?>" class="btn btn-xs btn-danger pull-right btn-helper"><i class="fa fa-reply"></i>Wróć do listy</a>
    </div>
    <div class="panel-body">
        <div class="bs-callout bs-callout-info">
            <p>
                Jesteś pewien że chcesz usunać raport ID: <a href="<?= $app->generateUrl('reports_view', array('reportID' => $report->getId())) ?>"><?= $report->getId() ?></a>?
            </p>
        </div>
        <form class="form-horizontal" role="form" method="post" action="<?= $app->generateUrl('reports_delete', array('reportID' => $report->getId())) ?>">
            <div class="text-center">
                <input type="hidden" name="id" value="<?= $report->getId() ?>">
                <button type="submit" class="btn btn-success"><i class="fa fa-thumbs-o-up"></i> Usuń</button>
                <a href="<?= $app->generateUrl('reports_index') ?>" class="btn btn-danger"><i class="fa fa-thumbs-o-down"></i> Anuluj</a>
            </div>
        </form>
    </div>
</div>