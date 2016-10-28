<?php $view->extend('content') ?>

<?php $view['slots']->set('_title', 'Logowanie - '.$app->getConfig()->anime->get('title')) ?>
<?php $view['slots']->set('_sidebar', $view->render('Sidebar/faq', array('sidebar' => $sidebar))) ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="glyphicon glyphicon-lock"></i> Logowanie</h3>
    </div>
    <div class="panel-body">
        <div class="col-md-7 col-sm-offset-2">
            <form class="form-horizontal" role="form" method="post" action="<?= $app->generateUrl('login_mobile') ?>">
                <div class="form-group">
                    <a href="<?= $app->generateUrl('login_facebook') ?>" class="btn btn-primary btn-block"><i class="fa fa-facebook-square"></i> Zaloguj przez Facebook</a>
                </div>
                <div class="form-group">
                    <label for="inputLogin">Użytkownik lub E-mail</label>
                    <input type="text" id="inputLogin" class="form-control" name="login" placeholder="Użytkownik lub E-mail" required="true">
                </div>
                <div class="form-group">
                    <label for="inputLogin">Hasło</label>
                    <input type="password" id="inputLogin" class="form-control" name="password" placeholder="Hasło" required="true">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-default btn-block"><i class="fa fa-sign-in"></i> Zaloguj się</button>
                    <input type="hidden" name="rememberMe" value="true">
                </div>
            </form>
        </div>
    </div>
</div>