<?php $view->extend('content') ?>

<?php $view['slots']->set('_title', 'Przypomnienie hasła - '.$app->getConfig()->anime->get('title')) ?>
<?php $view['slots']->set('_sidebar', $view->render('Sidebar/faq', array('sidebar' => $sidebar))) ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="glyphicon glyphicon-user"></i> Przypomnienie hasła</h3>
    </div>
    <div class="panel-body">
        <?php if(!empty($error)): ?>
            <div class="bs-callout bs-callout-danger">
                <p><?= $error ?></p>
            </div>
        <?php else: ?>
            <div class="bs-callout bs-callout-info">
                <p>Wpisane hasła muszą być identyczne.</p>
            </div>
        <?php endif ?>
        <form class="form-horizontal" role="form" method="post" action="<?= $app->generateUrl('user_restore_confirm', array('code' => $code)) ?>">
            <div class="form-group">
                <label for="inputPassword" class="col-sm-2 control-label">Hasło</label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" name="password" id="inputPassword" placeholder="Hasło" required title="Minimum 3 znaki, maksimum 32 znaków.">
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword2" class="col-sm-2 control-label">Powtórz hasło</label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" name="password2" id="inputPassword2" placeholder="Powtórz hasło" required title="Minimum 3 znaki, maksimum 32 znaków.">
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