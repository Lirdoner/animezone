<?php $view->extend('content') ?>

<?php $view['slots']->set('_sidebar_addon', $view->render('Sidebar/episodes/link_search', array('name' => $name, 'title' => $title, 'number' => $number, 'server_id' => $server_id, 'lang_id' => $lang_id))) ?>
<?php $view['slots']->set('_sidebar', $view->render('Sidebar/episodes/layout', array('current' => 'links'))) ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-link"></i> Lista linków <span class="badge"><?= $total ?></span></h3>
        <a href="<?= $app->generateUrl('links_index') ?>" class="btn btn-xs btn-danger pull-right btn-helper"><i class="fa fa-reply"></i>Wróć do listy</a>
    </div>
    <?php if($list->rowCount()): ?>
        <form method="post" action="<?= $app->generateUrl('links_update') ?>">
            <table class="table table-bordered table-striped table-hover" style="margin-bottom: 0">
                <thead>
                <tr>
                    <th colspan="2">Anime</th>
                    <th class="text-center col-sm-2">Serwer</th>
                    <th class="text-center col-sm-1">Język</th>
                    <th class="text-center col-sm-1">Odcinek</th>
                    <th class="text-center col-sm-1">Akcje</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($list->fetchAll() as $row): ?>
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" name="delete[]" value="<?= $row['id'] ?>" class="checkbox-inline">
                        </td>
                        <td class="col-sm-7">
                            <a href="<?= $app->generateUrl('categories_edit', array('catID' => $row['category_id'])) ?>" target="_blank"><?= $view['text']->truncate($row['anime'], 50) ?> <i class="fa fa-external-link"></i></a>
                        </td>
                        <td class="text-center">
                            <a href="<?= $app->generateUrl('servers_edit', array('serverID' => $row['server_id'])) ?>" target="_blank"><?= $row['name'] ?> <i class="fa fa-external-link"></i></a>
                        </td>
                        <td class="text-center"><?= $row['lang'] ?></td>
                        <td class="text-center">
                            <a href="<?= $app->generateUrl('episodes_edit', array('episodeID' => $row['episode_id'])) ?>" target="_blank"><?= $row['number'] ?> <i class="fa fa-external-link"></i></a>
                        </td>
                        <td class="text-center" style="padding: 8px 0 0 0">
                            <div class="btn-group btn-group-xs">
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                    Akcje <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu text-left" role="menu">
                                    <li><a href="<?= $app->generateUrl('links_edit', array('linkID' => $row['id'])) ?>"><i class="fa fa-edit"></i> Edytuj</a></li>
                                    <li><a href="<?= $app->generateUrl('links_delete', array('linkID' => $row['id'])) ?>"><i class="fa fa-times"></i> Usuń</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
            <div class="panel-body">
                <div class="checkbox" style="margin-top: 0; margin-bottom: 0; margin-left: -2px">
                    <label><input type="checkbox" class="select-all"> Zaznacz wszystkie</label> <button type="submit" class="btn btn-xs btn-default">usuń</button>
                    <?= $pagination ?>
                </div>
            </div>
        </form>
    <?php else: ?>
        <div class="panel-body">
            <div class="alert alert-info fade in" style="margin-bottom: 0">
                Brak pozycji do wyświetlenia.<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            </div>
        </div>
    <?php endif ?>
</div>