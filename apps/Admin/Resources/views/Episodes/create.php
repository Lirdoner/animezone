<?php $view->extend('content') ?>

<?php $view['slots']->set('_sidebar_addon', $view->render('Sidebar/episodes/episode_search')) ?>
<?php $view['slots']->set('_sidebar', $view->render('Sidebar/episodes/layout', array('current' => 'episodes'))) ?>

<style>
    .popover-title {
        display: none;
    }
</style>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="glyphicon glyphicon-plus-sign"></i> Nowy odcinek</h3>
        <a href="<?= $app->generateUrl('episodes_index') ?>" class="btn btn-xs btn-danger pull-right btn-helper"><i class="fa fa-reply"></i>Wróć do listy</a>
    </div>
    <div class="panel-body">
        <form class="form-horizontal" role="form" method="post" action="<?= $app->generateUrl('episodes_create') ?>">
            <div class="form-group">
                <label for="_category" class="col-sm-2 control-label">Kategoria</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="_category" autocomplete="off" required>
                    <input type="hidden" name="episode[category_id]" id="category_id">
                    <span class="help-block">Kategoria musi istnieć i zostać wybrana z listy.</span>
                </div>
            </div>
            <div class="form-group number">
                <label for="number" class="col-sm-2 control-label">Numer</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" name="episode[number]" id="number" placeholder="Numer odcinka" required>
                </div>
            </div>
            <div class="form-group">
                <label for="title" class="col-sm-2 control-label">Tytuł</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="episode[title]" id="title" placeholder="Tytuł odcinka" pattern=".{1,250}" autocomplete="off" title="Maksymalnie 250 znaków.">
                </div>
            </div>
            <div class="form-group">
                <label for="enabled" class="col-sm-2 control-label">Status</label>
                <div class="col-sm-10">
                    <select class="form-control" id="enabled" name="episode[enabled]">
                        <option value="1" selected>Działający</option>
                        <option value="0">Uszkodzony</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="filler" class="col-sm-2 control-label">Filler</label>
                <div class="col-sm-10">
                    <select class="form-control" id="filler" name="episode[filler]">
                        <option value="0" selected>Nie</option>
                        <option value="1">Tak</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" id="submit" class="btn btn-default">Wyślij</button>
                </div>
            </div>
        </form>
    </div>
</div>