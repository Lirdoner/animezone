<?php $view->extend('content') ?>

<?php $view['slots']->set('_sidebar', $view->render('Sidebar/categories/layout', array('current' => 'species'))) ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="glyphicon glyphicon-ok-sign"></i> Aktualizuj serię</h3>
        <a href="<?= $app->generateUrl('series_index') ?>" class="btn btn-xs btn-danger pull-right btn-helper"><i class="fa fa-reply"></i>Wróć do listy</a>
    </div>
    <div class="panel-body">
        <form class="form-horizontal" role="form" method="post" action="<?= $app->generateUrl('series_edit', array('rowID' => $cycle->getId())) ?>">
            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">Nazwa</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="cycle[name]" value="<?= $cycle->getName() ?>" id="name" placeholder="Nazwa serii" pattern=".{1,100}" required title="Maksimum 50 znaków.">
                    <input type="hidden" name="cycle[id]" value="<?= $cycle->getId() ?>">
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-default">Aktualizuj</button>
                </div>
            </div>
        </form>
    </div>
</div>