<?php $view->extend('content') ?>

<?php $view['slots']->set('_sidebar_addon', $view->render('Sidebar/users/session_search')) ?>
<?php $view['slots']->set('_sidebar', $view->render('Sidebar/users/layout', array('current' => 'sessions'))) ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-database"></i> Lista sesji <span class="badge"><?= $total ?></span></h3>
        <a href="<?= $app->generateUrl('sessions_delete', array('sessID' => 'all')) ?>" class="btn btn-xs btn-danger pull-right btn-helper"><i class="fa fa-times"></i> Usuń wszystkie sesje</a>
    </div>
    <?php if($list->rowCount()): ?>
        <form method="post" action="<?= $app->generateUrl('sessions_update') ?>">
            <table class="table table-bordered table-striped table-hover" style="margin-bottom: 0">
                <thead>
                <tr>
                    <th colspan="2">Użytkownik</th>
                    <th class="text-center" style="width: 20%">Adres IP</th>
                    <th class="text-center" style="width: 20%">Data aktualizacji</th>
                    <th class="text-center col-sm-1">Akcje</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($list->fetchAll() as $row): ?>
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" name="delete[]" value="<?= $row['sess_id'] ?>" class="checkbox-inline">
                        </td>
                        <td class="col-sm-6">
                            <a href="<?= $app->generateUrl('sessions_view', array('sessID' => $row['sess_id'])) ?>" class="toggle-tooltip" data-toggle="tooltip" title="<?= $row['user_agent'] ?>"><?= $row['name'] ?: 'Gość' ?></a>
                        </td>
                        <td class="text-center"><a href="<?= $app->generateUrl('sessions_search', array('user_ip' => $row['user_ip'])) ?>"><?= $row['user_ip'] ?> <i class="fa fa-external-link"></i></a></td>
                        <td class="text-center"><?= $row['last_active'] ?></td>
                        <td class="text-center" style="padding: 8px 0 0 0">
                            <div class="btn-group btn-group-xs">
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                    Akcje <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu text-left" role="menu">
                                    <li><a href="<?= $app->generateUrl('sessions_view', array('sessID' => $row['sess_id'])) ?>"><i class="fa fa-eye"></i> Szczegóły</a></li>
                                    <li><a href="<?= $app->generateUrl('sessions_delete', array('sessID' => $row['sess_id'])) ?>"><i class="fa fa-times"></i> Usuń</a></li>
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