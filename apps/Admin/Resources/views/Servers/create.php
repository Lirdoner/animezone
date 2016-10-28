<?php $view->extend('content') ?>

<?php $view['slots']->set('_sidebar', $view->render('Sidebar/episodes/layout', array('current' => 'servers'))) ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="glyphicon glyphicon-plus-sign"></i> Nowy serwer</h3>
        <a href="<?= $app->generateUrl('servers_index') ?>" class="btn btn-xs btn-danger pull-right btn-helper"><i class="fa fa-reply"></i>Wróć do listy</a>
    </div>
    <div class="panel-body">
        <form class="form-horizontal" role="form" method="post" action="<?= $app->generateUrl('servers_create') ?>">
            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">Nazwa</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="server[name]" id="name" placeholder="Nazwa serwera" pattern=".{1,30}" required title="Maksymalnie 30 znaków.">
                </div>
            </div>
            <div class="form-group">
                <label for="mobile" class="col-sm-2 control-label">Mobile</label>
                <div class="col-sm-10">
                    <select class="form-control" id="mobile" name="server[mobile]">
                        <option value="0">Nie</option>
                        <option value="1">Tak</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="template" class="col-sm-2 control-label">Szablon kodu</label>
                <div class="col-sm-10">
                    <textarea class="form-control" name="server[template]" id="template" rows="6"></textarea>
                    <span class="help-block">Nie zapomnij umieścić znacznik <kbd>{CODE}</kbd> który zostanie podmieniony na kod podany w linku.</span>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-default">Wyślij</button>
                </div>
            </div>
        </form>
    </div>
</div>