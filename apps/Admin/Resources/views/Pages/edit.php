<?php $view->extend('content') ?>

<?php $view['slots']->set('_sidebar', $view->render('Sidebar/content', array('current' => 'pages'))) ?>

<style>
    .popover-title {
        display: none;
    }
</style>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="glyphicon glyphicon-ok-sign"></i> Aktualizuj podstronę</h3>
        <a href="<?= $app->generateUrl('pages_index') ?>" class="btn btn-xs btn-danger pull-right btn-helper"><i class="fa fa-reply"></i>Wróć do listy</a>
    </div>
    <div class="panel-body">
        <form class="form-horizontal" role="form" method="post" action="<?= $app->generateUrl('pages_edit', array('pageID' => $page->getId())) ?>">
            <div class="form-group">
                <label for="_name" class="col-sm-2 control-label">Tytuł</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="page[name]" id="_name" value="<?= $page->getName() ?>" pattern=".{1,255}" required title="Maksymalnie 255 znaków." autocomplete="off">
                </div>
            </div>
            <div class="form-group">
                <label for="_alias" class="col-sm-2 control-label">Alias</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="page[alias]" id="_alias" pattern="[\w-]{1,255}" value="<?= $page->getAlias() ?>" title="Maksimum 255 alfanumerycznych znaków." autocomplete="off" data-type="pages">
                    <span class="help-block">Alias jest tworzony automatycznie na podstawie nazwy, lecz można go poprawić ręcznie.</span>
                </div>
            </div>
            <div class="form-group">
                <label for="_content" class="col-sm-2 control-label">Treść</label>
                <div class="col-sm-10">
                    <textarea class="form-control" name="page[content]" id="_content" rows="15" required><?= $page->getContent() ?></textarea>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <input type="hidden" name="page[id]" value="<?= $page->getId() ?>">
                    <button type="submit" id="submit" class="btn btn-default">Wyślij</button>
                </div>
            </div>
        </form>
    </div>
</div>