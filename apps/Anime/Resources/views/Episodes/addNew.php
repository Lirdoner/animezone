<?php $view->extend('content') ?>

<?php $view['slots']->set('_title', 'Dodaj odcinek - '.$app->getConfig()->anime->get('title')) ?>
<?php $view['slots']->set('_sidebar', $view->render('Sidebar/faq', array('sidebar' => $sidebar))) ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="glyphicon glyphicon-plus-sign"></i> Dodaj odcinek</h3>
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
                <p>W pole &bdquo;Link&rdquo; można wkleić większą ilość linków do odcinków, umieszczając kolejne jeden pod drugim, lecz poprzedzone numerem odcinka.</p>
            </div>
        <?php endif ?>
        <form class="form-horizontal" role="form" method="post" action="<?= $app->generateUrl('add_episode') ?>">
            <div class="form-group">
                <label for="inputTitle" class="col-sm-2 control-label">Tytuł</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="<?= $episode->getTitle() ?>" name="episode[title]" id="inputTitle" placeholder="Tytuł" pattern=".{6,200}" required title="Minimum 6 znaków, maksimum 200.">
                </div>
            </div>
            <div class="form-group">
                <label for="inputMessage" class="col-sm-2 control-label">Linki</label>
                <div class="col-sm-10">
                    <textarea class="form-control" name="episode[links]" id="inputMessage" rows="9" required><?= $episode->getLinks() ?></textarea>
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