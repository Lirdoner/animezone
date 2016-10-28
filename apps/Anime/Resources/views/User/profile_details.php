<?php $view->extend('content') ?>

<?php $view['slots']->set('_title', 'Lista '.strtolower($tabs[$action]).' - '.$user->getUsername().' - '.$app->getConfig()->anime->get('title')) ?>

<?php $view['slots']->start('_before_sidebar') ?>
    <div class="panel panel-transparent">
        <ul class="nav nav-pills nav-stacked">
            <?php foreach($tabs as $name => $value): ?>
                <li<?= $name == $action ? ' class="active"' : null ?>>
                    <a href="<?= $app->generateUrl('user_profile_details', array('user_name' => $user->getUsername(), 'action' => $name)) ?>">Lista <?= strtolower($value) ?> <span class="badge pull-right"><?= $user->getCustomField($name, 0) ?></span></a>
                </li>
            <?php endforeach ?>
        </ul>
    </div>
<?php $view['slots']->stop() ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="glyphicon glyphicon-align-justify"></i> <span class="title">Lista <?= $tabs[$action] ?></span></h3>
        <a href="<?= $app->generateUrl('user_profile', array('user_name' => $user->getUsername())) ?>" class="btn btn-xs btn-primary pull-right btn-helper"><i class="fa fa-reply"></i>Wróć do profilu</a>
    </div>
    <?php if('commented' !== $action): ?>
        <div class="panel-body user-activity">
            <?php if($list->rowCount()): ?>
                <?php foreach($list->fetchAll() as $row): ?>
                    <div class="well well-sm categories col-xs-12">
                        <a href="<?= $app->generateUrl('episodes_cat', array('cat' => $row['alias'])) ?>" class="image">
                            <img src="<?= $view['assets']->getUrl('kategorie/'.$row['image']) ?>" alt="" class="img-responsive lazy-loading">
                        </a>
                        <div class="title">
                            <p class="label label-grey text-center"><a href="<?= $app->generateUrl('episodes_cat', array('cat' => $row['alias'])) ?>"><?= $row['name'] ?></a></p>
                            <p class="info">
                                <small title="<?= $view['text']->timeElapsed($row['date'], true) ?>"><i class="glyphicon glyphicon-time"></i>Dodane <?= $view['text']->timeElapsed($row['date']) ?></small>
                                <?php if(isset($row['value'])): ?><span class="pull-right label label-dark"><i class="fa fa-star yellow"></i> Ocena <?= $row['value'] ?>/10</span><?php endif ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach ?>
            <?php else: ?>
                <div class="alert alert-info fade in">
                    Brak danych do wyświetlenia. <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                </div>
            <?php endif ?>
        </div>
    <?php else: ?>
        <div class="panel-body comments-list">
            <?php if($list->rowCount()): ?>
                <?php foreach($list as $comment): ?>
                    <div class="comment">
                        <div class="avatar pull-left">
                            <img src="<?= $view['text']->avatar($comment['avatar'], $view['assets']) ?>" class="img-responsive img-thumbnail">
                        </div>
                        <div class="comment-body pull-left">
                            <div class="comment-info">
                                @<strong><?= $comment['name'] ?></strong> &#8226; <small title="<?= $view['text']->timeElapsed($comment['date'], true) ?>"><?= $view['text']->timeElapsed($comment['date']) ?></small>
                                <span class="pull-right">@<a href="<?= $app->generateUrl('comments_redirect', array('commentID' => $comment['id'])) ?>" target="_blank"><?php if(!$comment['type']): ?>Anime<?php elseif(1 == $comment['type']): ?>Odcinek<?php elseif(2 == $comment['type']): ?>News<?php endif ?></a></span>
                            </div>
                            <div class="comment-message">
                                <?= $view['text']->bbcode($comment['message']) ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                <?php endforeach ?>
            <?php else: ?>
                <div class="alert alert-info fade in">
                    Brak danych do wyświetlenia. <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                </div>
            <?php endif ?>
        </div>
    <?php endif ?>
    <div class="panel-body text-center">
        <?= $pagination ?>
    </div>
</div>