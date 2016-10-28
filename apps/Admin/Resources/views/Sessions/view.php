<?php $view->extend('content') ?>

<?php $view['slots']->set('_sidebar_addon', $view->render('Sidebar/users/session_search')) ?>
<?php $view['slots']->set('_sidebar', $view->render('Sidebar/users/layout', array('current' => 'users'))) ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-eye"></i> Podgląd sesji</h3>
        <a href="<?= $app->generateUrl('sessions_index') ?>" class="btn btn-xs btn-danger pull-right btn-helper"><i class="fa fa-reply"></i>Wróć do listy</a>
    </div>
    <table class="table table-bordered table-striped">
        <tbody>
        <tr>
            <td class="col-md-2"><strong>ID sesji</strong></td>
            <td><?= $session['sess_id'] ?></td>
        </tr>
        <tr>
            <td class="col-md-2"><strong>ID użytkownika</strong></td>
            <td>
                <?= $session['user_id'] ?>
                <?php if($session['user_id']): ?>
                    - <a href="<?= $app->generateUrl('users_edit', array('userID' => $session['user_id'])) ?>">Sprawdź dane tego użytkownika <i class="fa fa-external-link"></i></a>
                <?php endif ?>
            </td>
        </tr>
        <tr>
            <td class="col-md-2"><strong>User Agent</strong></td>
            <td><?= $session['user_agent'] ?></td>
        </tr>
        <tr>
            <td class="col-md-2"><strong>Adres IP</strong></td>
            <td><a href="<?= $app->generateUrl('sessions_search', array('user_ip' => $session['user_ip'])) ?>"><?= $session['user_ip'] ?> <i class="fa fa-external-link"></i></a></td>
        </tr>
        <tr>
            <td class="col-md-2"><strong>Uprawnienia</strong></td>
            <td><?= $session['user_role'] ?></td>
        </tr>
        <tr>
            <td class="col-md-2"><strong>Data utworzenia</strong></td>
            <td><?= $session['date_created'] ?></td>
        </tr>
        <tr>
            <td class="col-md-2"><strong>Data aktualizacji</strong></td>
            <td><?= $session['last_active'] ?></td>
        </tr>
        </tbody>
    </table>
</div>