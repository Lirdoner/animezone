<?php $view->extend('content') ?>

<?php $view['slots']->set('_sidebar', $view->render('Sidebar/content', array('current' => 'ads'))) ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-delicious"></i> Lista reklam <span class="badge"><?= $total ?></span></h3>
        <a href="<?= $app->generateUrl('ads_create') ?>" class="btn btn-xs btn-success pull-right btn-helper"><i class="fa fa-plus-square"></i>Utwórz nową</a>
    </div>
    <?php if($list->rowCount()): ?>
        <form method="post" action="<?= $app->generateUrl('ads_update') ?>">
            <table class="table table-bordered table-striped table-hover" style="margin-bottom: 0">
                <thead>
                <tr>
                    <th colspan="2">Opis</th>
                    <th class="text-center col-sm-2">Alias</th>
                    <th class="text-center col-sm-1">Akcje</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($list->fetchAll() as $row): ?>
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" name="delete[]" value="<?= $row['id'] ?>" class="checkbox-inline">
                        </td>
                        <td class="col-sm-9"><?= $view['text']->truncate($row['description'] ?: 'brak', 85) ?></td>
                        <td class="text-center"><a href="<?= $app->generateUrl('ads_edit', array('adID' => $row['id'])) ?>"><?= $row['alias'] ?></a></td>
                        <td class="text-center" style="padding: 8px 0 0 0">
                            <div class="btn-group btn-group-xs">
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                    Akcje <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu text-left" role="menu">
                                    <li><a href="<?= $app->generateUrl('ads_edit', array('adID' => $row['id'])) ?>"><i class="fa fa-edit"></i> Edytuj</a></li>
                                    <li><a href="<?= $app->generateUrl('ads_delete', array('adID' => $row['id'])) ?>"><i class="fa fa-times"></i> Usuń</a></li>
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