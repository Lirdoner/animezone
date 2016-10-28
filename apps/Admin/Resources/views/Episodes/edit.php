<?php $view->extend('content') ?>

<?php $view['slots']->set('_sidebar_addon', $view->render('Sidebar/episodes/episode_search')) ?>
<?php $view['slots']->set('_sidebar', $view->render('Sidebar/episodes/layout', array('current' => 'episodes'))) ?>

<style>
    .popover-title {
        display: none;
    }
</style>
<div class="panel panel-default category-description">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="glyphicon glyphicon-plus-sign"></i> Aktualizuj odcinek</h3>
        <a href="<?= $app->generateUrl('episodes_index') ?>" class="btn btn-xs btn-danger pull-right btn-helper"><i class="fa fa-reply"></i>Wróć do listy</a>
    </div>
    <div class="panel-body">
        <form class="form-horizontal" role="form" method="post" action="<?= $app->generateUrl('episodes_edit', array('episodeID' => $episode->getId())) ?>">
            <div class="form-group">
                <label for="_category" class="col-sm-2 control-label">Kategoria</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="_category" autocomplete="off" value="<?= $category ?>" required>
                    <input type="hidden" name="episode[category_id]" id="category_id" value="<?= $episode->getCategoryId() ?>">
                    <span class="help-block">Kategoria musi istnieć i zostać wybrana z listy.</span>
                </div>
            </div>
            <div class="form-group number">
                <label for="number" class="col-sm-2 control-label">Numer</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" name="episode[number]" id="number" value="<?= $episode->getNumber() ?>" placeholder="Numer odcinka" required>
                </div>
            </div>
            <div class="form-group">
                <label for="title" class="col-sm-2 control-label">Tytuł</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="episode[title]" id="title" value="<?= $episode->getTitle() ?>" placeholder="Tytuł odcinka" pattern=".{1,250}" autocomplete="off" title="Maksymalnie 250 znaków.">
                </div>
            </div>
            <div class="form-group">
                <label for="status" class="col-sm-2 control-label">Status</label>
                <div class="col-sm-10">
                    <select class="form-control" id="status" name="episode[enabled]">
                        <option value="1"<?= $episode->getEnabled() ? ' selected' : null ?>>Działający</option>
                        <option value="0"<?= !$episode->getEnabled() ? ' selected' : null ?>>Uszkodzony</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="filler" class="col-sm-2 control-label">Filler</label>
                <div class="col-sm-10">
                    <select class="form-control" id="filler" name="episode[filler]">
                        <option value="0"<?= !$episode->getFiller() ? ' selected' : null ?>>Nie</option>
                        <option value="1"<?= $episode->getFiller() ? ' selected' : null ?>>Tak</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <input type="hidden" name="episode[id]" value="<?= $episode->getId() ?>">
                    <button type="submit" id="submit" class="btn btn-default">Wyślij</button>
                </div>
            </div>
        </form>
    </div>
    <?php if($list->rowCount()): ?>
    <h5>Linki</h5>
    <table class="table table-bordered table-striped">
        <thead class="bg-success">
        <tr>
            <th class="col-sm-9">Serwer</th>
            <th class="text-center col-sm-1">Język</th>
            <th class="text-center col-sm-1">Akcje</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($list->fetchAll() as $row): ?>
            <tr>
                <td><a href="<?= $app->generateUrl('servers_edit', array('serverID' => $row['server_id'])) ?>" target="_blank"><?= $row['name'] ?> <i class="fa fa-external-link"></i></a></td>
                <td class="text-center"><?= $row['lang'] ?></td>
                <td class="text-center" style="padding: 8px 0 0 0">
                    <a href="<?= $app->generateUrl('links_edit', array('linkID' => $row['id'])) ?>" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i> Edytuj</a>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
    <?php endif ?>
</div>