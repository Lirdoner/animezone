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
                <p>Nie pamiętasz hasła do swojego konta? Skorzystaj z formularza poniżej.</p>
            </div>
        <?php endif ?>
        <form class="form-horizontal" role="form" method="post" action="<?= $app->generateUrl('user_restore') ?>">
            <div class="form-group">
                <label for="inputEmail" class="col-sm-2 control-label">E-mail</label>
                <div class="col-sm-10">
                    <input type="email" required size="40" class="form-control" name="email" id="inputEmail" value="<?= $email ?>" placeholder="E-mail">
                </div>
            </div>
            <div class="form-group">
                <label for="inputCode" class="col-sm-2 control-label">Kod z obrazka</label>
                <div class="col-sm-10">
                    <div class="thumbnail image-captcha">
                        <img src="<?= $app->generateUrl('_captcha') ?>" class="img-responsive">
                    </div>
                    <div class="input-group input-captcha">
                        <input type="text" class="form-control" name="code" id="inputCode" placeholder="kod" required>
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button"><i class="glyphicon glyphicon-refresh"></i> </button>
                        </span>
                    </div>
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