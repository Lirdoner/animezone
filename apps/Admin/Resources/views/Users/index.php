<?php $view->extend('content') ?>

<?php $view['slots']->set('_sidebar_addon', $view->render('Sidebar/users/user_search')) ?>
<?php $view['slots']->set('_sidebar', $view->render('Sidebar/users/layout', array('current' => 'users'))) ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-users"></i> Lista Użytkowników <span class="badge"><?= $total ?></span></h3>
        <a href="<?= $app->generateUrl('users_create') ?>" class="btn btn-xs btn-success pull-right btn-helper"><i class="fa fa-plus-square"></i>Utwórz nowego</a>
    </div>
    <?php if($list->rowCount()): ?>
        <form method="post" action="<?= $app->generateUrl('users_update') ?>">
            <table class="table table-bordered table-striped table-hover" style="margin-bottom: 0">
                <thead>
                <tr>
                    <th colspan="3">Użytkownik</th>
                    <th class="text-center" style="width: 20%">Data rejestracji</th>
                    <th class="text-center col-sm-1">Akcje</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($list->fetchAll() as $row): ?>
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" name="delete[]" value="<?= $row['id'] ?>" class="checkbox-inline">
                        </td>
                        <td style="padding: 0">
                            <img src="<?= $view['text']->avatar($row['avatar'], $view['assets']) ?>" style="width:35px;height:35px">
                        </td>
                        <td class="col-sm-8"><a href="<?= $app->generateUrl('users_edit', array('userID' => $row['id'])) ?>"><?= $row['name'] ?></a></td>
                        <td class="text-center"><?= $row['date_created'] ?></td>
                        <td class="text-center" style="padding: 8px 0 0 0">
                            <div class="btn-group btn-group-xs">
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                    Akcje <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu text-left" role="menu">
                                    <li><a href="<?= $app->basePath('/user/'.$row['name']) ?>" target="_blank"><i class="fa fa-external-link"></i> Publiczny profil</a></li>
                                    <li><a href="<?= $app->generateUrl('users_edit', array('userID' => $row['id'])) ?>"><i class="fa fa-edit"></i> Edytuj</a></li>
                                    <?php if($row['enabled'] <= 1): ?>
                                        <li><a href="<?= $app->generateUrl('users_change', array('userID' => $row['id'], 'action' => 'enabled', 'value' => 2)) ?>"><i class="fa fa-lock"></i> Zablokuj</a></li>
                                    <?php elseif(2 == $row['enabled']): ?>
                                        <li><a href="<?= $app->generateUrl('users_change', array('userID' => $row['id'], 'action' => 'enabled', 'value' => 1)) ?>"><i class="fa fa-unlock"></i> Odblokuj</a></li>
                                    <?php endif ?>
                                    <?php if('ROLE_USER' == $row['role']): ?>
                                        <li><a href="<?= $app->generateUrl('users_change', array('userID' => $row['id'], 'action' => 'role', 'value' => 'ROLE_ADMIN')) ?>"><i class="fa fa-gavel "></i> Nadaj admina</a></li>
                                    <?php else: ?>
                                        <li><a href="<?= $app->generateUrl('users_change', array('userID' => $row['id'], 'action' => 'role', 'value' => 'ROLE_USER')) ?>"><i class="fa fa-gavel "></i> Odbierz admina</a></li>
                                    <?php endif ?>
                                    <li role="presentation" class="divider"></li>
                                    <li><a href="<?= $app->generateUrl('users_delete', array('userID' => $row['id'])) ?>"><i class="fa fa-times"></i> Usuń</a></li>
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