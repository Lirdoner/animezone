<?php $view->extend('content') ?>

<?php $view['slots']->set('_title', 'Kontakt - '.$app->getConfig()->anime->get('title')) ?>
<?php $view['slots']->set('_sidebar', $view->render('Sidebar/faq', array('sidebar' => $sidebar))) ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="glyphicon glyphicon-envelope"></i> Kontakt</h3>
    </div>
    <div class="panel-body">
        <?php if(!empty($error_msg)): ?>
            <div class="bs-callout bs-callout-danger">
                <h4>Jedno lub więcej pól formularza jest niepoprawne</h4>
                <p>
                    <?php foreach($error_msg as $msg): ?>
                        - <?= $msg ?><br>
                    <?php endforeach ?>
                </p>
            </div>
        <?php else: ?>
            <div class="bs-callout bs-callout-info">
                <p>Odpowiadamy tylko na wiadomości wysłane poprzez formularz kontaktowy!</p>
            </div>
        <?php endif ?>
        <form class="form-horizontal" role="form" method="post" action="<?= $app->generateUrl('contact') ?>">
            <div class="form-group">
                <label for="inputTitle" class="col-sm-2 control-label">Temat</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="<?= $report->getSubject() ?>" name="report[subject]" id="inputTitle" placeholder="Temat" pattern=".{6,100}" required title="Minimum 6 znaków, maksimum 100.">
                </div>
            </div>
            <?php if(!$app->getUser()->isUser()): ?>
                <div class="form-group">
                    <label for="inputEmail" class="col-sm-2 control-label">E-mail</label>
                    <div class="col-sm-10">
                        <input type="email" required size="40" class="form-control" value="<?= $report->getMail() ?>" name="report[mail]" id="inputEmail" placeholder="E-mail">
                    </div>
                </div>
            <?php endif ?>
            <div class="form-group">
                <label for="inputMessage" class="col-sm-2 control-label">Wiadomość</label>
                <div class="col-sm-10">
                    <textarea class="form-control" name="report[content]" id="inputMessage" rows="9" required><?= $report->getContent() ?></textarea>
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