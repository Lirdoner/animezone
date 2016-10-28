<?php $view->extend('content') ?>

<?php $view['slots']->set('_sidebar', $view->render('Sidebar/stats')) ?>
<?php $view['slots']->start('_footer') ?>
<script src="<?= $view['assets']->getUrl('javascript/jquery.shorten.js') ?>"></script>
<script src="<?= $view['assets']->getUrl('javascript/dashboard.js') ?>"></script>
<?php $view['slots']->stop() ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-weixin"></i> Notatka</h3>
    </div>
    <div class="panel-body">
        <textarea class="form-control" id="note"><?= isset($note['text']) ? $note['text'] : null ?></textarea>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-code-fork"></i> Poczekalnia</h3>
        <a href="<?= $app->generateUrl('submitted_index') ?>" class="btn btn-xs btn-primary pull-right btn-helper"><i class="fa fa-reply"></i>Zobacz więcej</a>
    </div>
    <?php if($submitted->rowCount()): ?>
        <form method="post" action="<?= $app->generateUrl('submitted_update') ?>">
            <table class="table table-bordered table-striped table-hover" style="margin-bottom: 0">
                <thead>
                <tr>
                    <th colspan="2">Tytuł</th>
                    <th class="text-center col-sm-3">Data</th>
                    <th class="text-center col-sm-1">Akcje</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($submitted->fetchAll() as $row): ?>
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" name="delete[]" value="<?= $row['id'] ?>" class="checkbox-inline">
                        </td>
                        <td class="col-sm-8">
                            <a href="<?= $app->generateUrl('submitted_view', array('episodeID' => $row['id'])) ?>"><?= $view['text']->truncate(htmlspecialchars($row['title']), 65) ?></a>
                        </td>
                        <td class="text-center"><?= $view['text']->timeElapsed($row['date']) ?></td>
                        <td class="text-center" style="padding: 8px 0 0 0">
                            <div class="btn-group btn-group-xs">
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                    Akcje <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu text-left" role="menu">
                                    <li><a href="<?= $app->generateUrl('submitted_view', array('episodeID' => $row['id'])) ?>"><i class="fa fa-eye"></i> Podgląd</a></li>
                                    <li><a href="<?= $app->generateUrl('submitted_delete', array('episodeID' => $row['id'])) ?>"><i class="fa fa-times"></i> Usuń</a></li>
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

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-bug"></i> Ostatnie raporty</h3>
        <a href="<?= $app->generateUrl('reports_index') ?>" class="btn btn-xs btn-primary pull-right btn-helper"><i class="fa fa-reply"></i>Zobacz więcej</a>
    </div>
    <?php if($reports->rowCount()): ?>
        <form method="post" action="<?= $app->generateUrl('reports_update') ?>">
            <table class="table table-bordered table-striped table-hover" style="margin-bottom: 0">
                <thead>
                <tr>
                    <th colspan="2">Tytuł</th>
                    <th class="text-center col-sm-3">Data</th>
                    <th class="text-center col-sm-1">Akcje</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($reports->fetchAll() as $row): ?>
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" name="delete[]" value="<?= $row['id'] ?>" class="checkbox-inline">
                        </td>
                        <td class="col-sm-8">
                            <?php if(1 == $row['type']): ?>
                                <a href="<?= $app->generateUrl('reports_view', array('reportID' => $row['id'])) ?>" target="_blank">Błędny link: <?= $row['link_id'] ?> <i class="fa fa-external-link"></i></a>
                            <?php elseif(2 == $row['type']): ?>
                                <a href="<?= $app->generateUrl('reports_view', array('reportID' => $row['id'])) ?>" target="_blank">Błędny komentarz: <?= $row['link_id'] ?> <i class="fa fa-external-link"></i></a>
                            <?php elseif(3 == $row['type']): ?>
                                <a href="<?= $app->generateUrl('reports_view', array('reportID' => $row['id'])) ?>"><?= $view['text']->truncate(htmlspecialchars($row['subject']), 70) ?></a>
                            <?php endif ?>
                        </td>
                        <td class="text-center"><?= $view['text']->timeElapsed($row['date']) ?></td>
                        <td class="text-center" style="padding: 8px 0 0 0">
                            <div class="btn-group btn-group-xs">
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                    Akcje <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu text-left" role="menu">
                                    <li><a href="<?= $app->generateUrl('reports_delete', array('reportID' => $row['id'])) ?>"><i class="fa fa-times"></i> Usuń</a></li>
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

<div class="panel panel-default" id="comments">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="glyphicon glyphicon-comment"></i> Ostatnie komentarze</h3>
        <a href="<?= $app->generateUrl('comments_index') ?>" class="btn btn-xs btn-primary pull-right btn-helper"><i class="fa fa-reply"></i>Zobacz więcej</a>
    </div>
    <div class="panel-body comments-list">
        <div id="commentlist">
            <?php foreach($comments as $comment): ?>
                <div class="comment">
                    <div class="avatar pull-left">
                        <a href="<?= $app->basePath('/user/'.$comment['name']) ?>">
                            <img src="<?= $view['text']->avatar($comment['avatar'], $view['assets']) ?>" class="img-responsive img-thumbnail">
                        </a>
                    </div>
                    <div class="comment-body pull-left">
                        <p class="comment-info">
                            @<a href="<?= $app->basePath('/user/'.$comment['name']) ?>"<?php echo $comment['role'] == 'ROLE_ADMIN' ? ' class="text-danger"' : null ?>><strong><?= $comment['name'] ?></strong></a> &#8226;
                            <small title="<?= $view['text']->timeElapsed($comment['date'], true) ?>"><?= $view['text']->timeElapsed($comment['date']) ?></small> &#8226;
                            <a href="<?= $app->generateUrl('comments_edit', array('commentID' => $comment['id'])) ?>">edytuj</a> &#8226;
                            <a href="<?= $app->generateUrl('comments_delete', array('commentID' => $comment['id'])) ?>">usuń</a>
                            <span class="pull-right">@<a href="<?= $app->basePath('/comments/redirect/'.$comment['id']) ?>" target="_blank"><?php if(!$comment['type']): ?>Anime<?php elseif(1 == $comment['type']): ?>Odcinek<?php elseif(2 == $comment['type']): ?>News<?php endif ?></a></span>
                        </p>
                        <p class="comment-message">
                            <?= $view['text']->bbcode($comment['message']) ?>
                        </p>
                    </div>
                    <div class="clearfix"></div>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</div>