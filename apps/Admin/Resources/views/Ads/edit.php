<?php $view->extend('content') ?>

<?php $view['slots']->set('_sidebar', $view->render('Sidebar/content', array('current' => 'ads'))) ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="glyphicon glyphicon-ok-sign"></i> Aktualizuj reklamę</h3>
        <a href="<?= $app->generateUrl('ads_index') ?>" class="btn btn-xs btn-danger pull-right btn-helper"><i class="fa fa-reply"></i>Wróć do listy</a>
    </div>
    <div class="panel-body">
        <form class="form-horizontal" role="form" method="post" action="<?= $app->generateUrl('ads_edit', array('adID' => $ad->getId())) ?>">
            <div class="form-group">
                <label for="alias" class="col-sm-2 control-label">Alias</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="ads[alias]" id="alias" value="<?= $ad->getAlias() ?>" pattern="[\w-]{1,20}" required title="Maksymalnie 20 znaków alfanumerycznych.">
                </div>
            </div>
            <div class="form-group">
                <label for="code" class="col-sm-2 control-label">Kod</label>
                <div class="col-sm-10">
                    <textarea class="form-control" name="ads[code]" id="code" rows="15" required><?= $ad->getCode() ?></textarea>
                </div>
            </div>
            <div class="form-group">
                <label for="description" class="col-sm-2 control-label">Opis</label>
                <div class="col-sm-10">
                    <textarea class="form-control" name="ads[description]" id="description" rows="8"><?= $ad->getDescription() ?></textarea>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <input type="hidden" name="ads[id]" value="<?= $ad->getId() ?>">
                    <button type="submit" class="btn btn-default">Wyślij</button>
                </div>
            </div>
        </form>
    </div>
</div>