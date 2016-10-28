<?php $view->extend('content') ?>

<?php $view['slots']->set('_sidebar', $view->render('Sidebar/users/layout', array('current' => 'comments'))) ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="glyphicon glyphicon-ok-sign"></i> Aktualizuj podstronę</h3>
        <div class="pull-right btn-helper">
            <a href="<?= $app->generateUrl('comments_index') ?>" class="btn btn-xs btn-danger"><i class="fa fa-reply"></i>Wróć do listy</a>
            <a href="<?= $app->basePath('/comments/redirect/'.$comment->getId()) ?>" class="btn btn-xs btn-primary" target="_blank">Link publiczny <i class="fa fa-external-link"></i></a>
        </div>
    </div>
    <div class="panel-body">
        <form class="form-horizontal" role="form" method="post" action="<?= $app->generateUrl('comments_edit', array('commentID' => $comment->getId())) ?>">
            <div class="form-group">
                <label for="to" class="col-sm-2 control-label">Przypisane</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" name="comment[to]" id="to" value="<?= $comment->getTo() ?>">
                    <span class="help-block">
                        <a href="<?= $app->generateUrl('comments_search', array('to' => $comment->getTo(), 'type' => $comment->getType())) ?>">Wyświetl wszystkie komentarze do
                        <?php if(!$comment->getType()): ?>
                            kategorii
                         <?php elseif($comment->getType() == 1): ?>
                            odcinka
                         <?php elseif($comment->getType() == 2): ?>
                            newsa
                         <?php endif ?><i class="fa fa-external-link"></i>
                        </a>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <label for="type" class="col-sm-2 control-label">Typ</label>
                <div class="col-sm-10">
                    <select class="form-control" id="type" name="comment[type]">
                        <option value="0"<?= !$comment->getType() ? ' selected' : null ?>>Kategoria</option>
                        <option value="1"<?= $comment->getType() == 1 ? ' selected' : null ?>>Odcinki</option>
                        <option value="2"<?= $comment->getType() == 2 ? ' selected' : null ?>>Newsy</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="user_id" class="col-sm-2 control-label">Użytkownik</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" name="comment[user_id]" id="user_id" value="<?= $comment->getUserId() ?>">
                    <span class="help-block"><a href="<?= $app->generateUrl('comments_search', array('user_id' => $comment->getUserId())) ?>">Wyświetl wszystkie komentarze tego użytkownika <i class="fa fa-external-link"></i></a></span>
                </div>
            </div>
            <div class="form-group">
                <label for="message" class="col-sm-2 control-label">Treść</label>
                <div class="col-sm-10">
                    <textarea class="form-control" name="comment[message]" id="message" rows="15" required><?= $comment->getMessage() ?></textarea>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <input type="hidden" name="comment[id]" value="<?= $comment->getId() ?>">
                    <button type="submit" id="submit" class="btn btn-default">Wyślij</button>
                </div>
            </div>
        </form>
    </div>
</div>