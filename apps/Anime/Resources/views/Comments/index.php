<div class="panel panel-default" id="comments">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="glyphicon glyphicon-comment"></i> Komentarze <span class="badge">0</span></h3>
    </div>
    <div class="panel-body comments-list">
        <p class="text-center loader hidden"><img src="<?= $view['assets']->getUrl('images/ajax-loader.gif') ?>"></p>
        <div id="commentlist" data-to="<?= $to ?>" data-type="<?= $type ?>"></div>
        <?php if($app->getUser()->isUser()): ?>
            <form id="addComment" class="form-horizontal">
                <textarea class="form-control" rows="5" name="message"></textarea>
                <p class="pull-left help-block">
                    Licznik znaków (minimum 40 znaków): <span class="label label-danger counter">0</span>
                </p>
                <div class="pull-right help-block">
                    <div class="btn-group btn-group-xs">
                        <button type="button" class="btn btn-default" title="Pogrubienie" data-bbcode="b"><strong>B</strong></button>
                        <button type="button" class="btn btn-default" title="Kursywa" data-bbcode="i"><em>I</em></button>
                        <button type="button" class="btn btn-default" title="Podkreślenie" data-bbcode="u"><ins>U</ins></button>
                        <button type="button" class="btn btn-default" title="Cytuj" data-bbcode="quote"><i class="fa fa-quote-right"></i></button>
                        <button type="button" class="btn btn-default" data-bbcode="spoiler">spoiler</button>
                        <?php if($app->getUser()->isAdmin()): ?><button type="button" class="btn btn-default" data-bbcode="admin">admin</button><?php endif ?>
                    </div>
                    <button type="submit" class="btn btn-xs btn-primary disabled">Dodaj komentarz</button>
                    <input type="hidden" value="<?= $type ?>" name="type">
                    <input type="hidden" value="<?= $to ?>" name="to">
                </div>
                <div class="clearfix"></div>
            </form>
        <?php else: ?>
            <div class="alert alert-info fade in">
                Musisz być zalogowany aby komentować! <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            </div>
        <?php endif ?>
    </div>
</div>