<form id="editComment" class="form-horizontal">
    <textarea class="form-control" rows="5" name="message"><?= $comment['message'] ?></textarea>
    <p class="pull-left help-block">
        Minimum 40 znaków: <span class="label label-danger counter"><?= strlen($comment['message']) ?></span>
    </p>
    <div class="pull-right help-block">
        <div class="btn-group btn-group-xs">
            <button type="button" class="btn btn-default" title="Pogrubienie" data-bbcode="b"><strong>B</strong></button>
            <button type="button" class="btn btn-default" title="Kursywa" data-bbcode="i"><em>I</em></button>
            <button type="button" class="btn btn-default" title="Podkreślenie" data-bbcode="u"><ins>U</ins></button>
            <button type="button" class="btn btn-default" title="Cytuj" data-bbcode="cytat"><i class="fa fa-quote-right"></i></button>
            <button type="button" class="btn btn-default" data-bbcode="ukryj">spoiler</button>
            <?php if($app->getUser()->isAdmin()): ?><button type="button" class="btn btn-default" data-bbcode="admin">admin</button><?php endif ?>
        </div>
        <button type="submit" class="btn btn-xs btn-primary disabled">Aktualizuj komentarz</button>
        <button type="button" class="btn btn-xs btn-danger finish-edit">Zakończ</button>
        <input type="hidden" name='id' value="<?php echo $comment['id'] ?>">
        <input type="hidden" value="<?= $comment['type'] ?>" name="type">
        <input type="hidden" value="<?= $comment['to'] ?>" name="to">
    </div>
    <div class="clearfix"></div>
</form>