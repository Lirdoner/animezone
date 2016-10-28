<?php $view->extend('content') ?>

<?php $view['slots']->set('_title', 'Profil użytkownika '.$user->getUsername().' - '.$app->getConfig()->anime->get('title')) ?>
<?php $view['slots']->set('_sidebar', $view->render('Sidebar/faq', array('sidebar' => $sidebar))) ?>

<div class="panel panel-default user-profile">
    <div class="panel-heading">
        <h3 class="panel-title">
            <i class="glyphicon glyphicon-user"></i> <span class="title">Profil użytkownika</span>
            <span class="label label-dark"><?= $user->getUsername() ?></span>
        </h3>
        <?php if($user->isAdmin()): ?><button class="pull-right btn btn-xs btn-danger active">Administrator</button><?php endif ?>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th class="text-center col-lg-3 col-md-3 col-sm-3 col-xs-2">Awatar</th>
                <th colspan="2" class="text-center">O użytkowniku</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td rowspan="4" class="text-center avatar">
                    <img src="<?= $view['text']->avatar($user->getCustomField('avatar'), $view['assets']) ?>" class="img-thumbnail img-responsive">
                </td>
                <td>
                    <strong>Zarejestrowany:</strong>
                    <span class="pull-right"><?= $view['text']->timeElapsed($user->getDateCreated(), 'date') ?></span>
                </td>
                <td>
                    <strong>Ostatnio widziany:</strong>
                    <span class="pull-right"><?= $view['text']->timeElapsed($user->getLastLogin()) ?></span>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Komentarze:</strong>
                    <span class="pull-right"><?= $user->getCustomField('commented', 0) ?></span>
                </td>
                <td>
                    <strong>Wiek:</strong>
                    <span class="pull-right"><?= $user->getCustomField('birthdate') == '0000-00-00' ? 'N/A' : date_diff(date_create('now'), date_create($user->getCustomField('birthdate')))->format('%y lat/a') ?></span>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Lokalizacja:</strong>
                    <span class="pull-right"><?= $user->getCustomField('location', 'N/A') ?></span>
                </td>
                <td>
                    <strong>Płeć:</strong>
                    <span class="pull-right"><?= 1 == $user->getCustomField('gender') ? '<i class="fa fa-male"></i> Mężczyzna' : '<i class="fa fa-female"></i> Kobieta' ?></span>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Ulubione:</strong>
                    <span class="pull-right"><?= $user->getCustomField('favorites', 0) ?></span>
                </td>
                <td>
                    <strong>Ocenione:</strong>
                    <span class="pull-right"><?= $user->getCustomField('rated', 0) ?></span>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="glyphicon glyphicon-list-alt"></i> <span class="title">MyAnimeList</span></h3>
    </div>
    <div class="panel-body user-activity">
        <?php if($data->rowCount()): ?>
			//myanimelist
        <?php endif ?>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="glyphicon glyphicon-align-justify"></i> <span class="title">Ostatnia aktywność</span></h3>
        <?php if($user->getCustomField($action, 0) > 6): ?>
            <a href="<?= $app->generateUrl('user_profile_details', array('user_name' => $user->getUsername(), 'action' => $action)) ?>" class="btn btn-xs btn-primary pull-right btn-helper"><i class="fa fa-list"></i>Zobacz więcej</a>
        <?php endif ?>
    </div>
    <div class="panel-body user-activity">
        <ul class="nav nav-pills nav-justified">
            <?php foreach($tabs as $name => $value): ?>
                <?php if($name == $action): ?>
                    <li class="active"><a href="#activity"><?php echo $value ?></a></li>
                <?php else: ?>
                    <li><a href="<?= $app->generateUrl('user_profile', array('user_name' => $user->getUsername(), 'action' => $name)) ?>"><?= $value ?></a></li>
                <?php endif ?>
            <?php endforeach ?>
        </ul>
        <hr>
        <?php if($data->rowCount()): ?>
            <?php foreach($data->fetchAll() as $row): ?>
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
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="glyphicon glyphicon-comment"></i> <span class="title">Ostatnie komentarze</span></h3>
        <?php if($user->getCustomField('commented', 0) > 10): ?>
            <a href="<?= $app->generateUrl('user_profile_details', array('user_name' => $user->getUsername(), 'action' => 'commented')) ?>" class="btn btn-xs btn-primary pull-right btn-helper"><i class="fa fa-list"></i>Zobacz więcej</a>
        <?php endif ?>
    </div>
    <div class="panel-body comments-list">
        <?php if($user->getCustomField('commented', false)): ?>
            <?php foreach($comments as $comment): ?>
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
                            <?= $view['text']->bbcode(nl2br($comment['message'])) ?>
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
</div>