<?php $view->extend('content') ?>

<?php $view['slots']->set('_title', htmlspecialchars($category->getName()).' - odcinki anime online') ?>
<?php $view['slots']->set('_description', $category->getName().' online - '.$view['text']->truncate(strip_tags($category->getDescription()), 100, '...')) ?>
<?php $view['slots']->set('_sidebar', $view->render('Sidebar/similar', array('sidebar' => $similar, 'similar_title' => $similar_title))) ?>

<?php $view['slots']->start('_head') ?>
    <meta property="og:site_name" content="AnimeZone.pl">
    <meta property="og:title" content="<?= $category->getName().' - odcinki anime online' ?>">
    <meta property="og:description" content="<?= $category->getName().' online - '.$view['text']->truncate(strip_tags($category->getDescription()), 100) ?>">
    <meta property="og:type" content="tv_show">
    <meta property="og:url" content="<?= $app->generateUrl('episodes_cat', array('cat' => $category->getAlias()), true) ?>">
    <meta property="og:image" content="http://<?= $app->getRequest()->getHost().$view['assets']->getUrl('kategorie/'.$category->getImage()) ?>">
<?php $view['slots']->stop() ?>

<?php $view['slots']->start('_footer') ?>
    <script src="<?= $view['assets']->getUrl('javascript/jquery.autosize.min.js') ?>"></script>
    <script src="<?= $view['assets']->getUrl('javascript/episode.js') ?>"></script>
    <script src="<?= $view['assets']->getUrl('javascript/comments.js') ?>"></script>
<?php $view['slots']->stop() ?>

<div class="panel panel-default category-description">
    <div class="panel-heading">
        <h3 class="panel-title" title="<?= $category->getName() ?>">
            <i class="glyphicon glyphicon-tag"></i> <?= htmlspecialchars($category->getName()) ?>
        </h3>
        <a href="<?= $app->generateUrl('contact') ?>?title=<?= urlencode('Błąd w anime: '.$category->getName()) ?>" class="pull-right btn btn-xs btn-danger"><i class="fa fa-warning"></i> Zgłoś błąd</a>
    </div>
    <div class="panel-body category-description-body">
        <div class="image pull-left">
            <img src="<?= $view['assets']->getUrl('kategorie/'.$category->getImage()) ?>" alt="" class="img-responsive">
        </div>
        <div class="basic-info pull-right">
            <table class="table table-bordered">
                <tbody>
                <tr>
                    <td style="width: 30%">Typ widowni</td>
                    <td><?= $types ? $types : 'brak' ?></td>
                </tr>
                <tr>
                    <td>Rok produkcji</td>
                    <td><?= $category->getYear() ?></td>
                </tr>
                <tr>
                    <td>Wiek widowni</td>
                    <td><?= $category->getPegi() ? $category->getPegi() : 'brak' ?></td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="clearfix"></div>
    </div>
    <h5>Informacje</h5>
    <table class="table table-bordered table-striped">
        <tbody>
            <tr>
                <td><strong>Alternatywny tytuł</strong></td>
                <td><?= $category->getAlternate() ? $category->getAlternate() : 'brak' ?></td>
            </tr>
            <tr>
                <td><strong>Rodzaj</strong></td>
                <td><?= $category->getRelease(true) ?></td>
            </tr>
            <tr>
                <td><strong>Gatunek</strong></td>
                <td><?= $species ? $species : 'brak' ?></td>
            </tr>
            <tr>
                <td><strong>Tematyka</strong></td>
                <td><?= $topics ? $topics : 'brak' ?></td>
            </tr>
            <tr>
                <td><strong>Status</strong></td>
                <td><?= $category->getStatus(true) ?></td>
            </tr>
        </tbody>
    </table>
    <h5>Opis</h5>
    <div class="panel-body" property="v:summary">
        <?= nl2br($category->getDescription()) ?>

        <?php if(!$app->getUser()->isAdmin()): ?>
            <div class="rek kategoria"><?= $ads->random('baner') ?></div>
        <?php endif ?>
    </div>
    <h5>Odcinki</h5>
    <?php if($episodes->rowCount()): ?>
        <table class="table table-bordered table-striped table-hover episodes">
            <thead class="bg-primary">
            <tr>
                <th style="width: 7%" class="text-center">Numer</th>
                <th class="episode-title">Tytuł</th>
                <th style="width: 7%" class="text-center">Wersja</th>
                <th style="width: 5%" class="text-center filler">Filler</th>
                <th style="width: 8%" class="text-center">Link</th>
            </tr>
            </thead>
            <tbody>
                <?php foreach($episodes->fetchAll() as $item => $episode): ?>
                    <tr>
                        <td class="text-center"><strong><?= $episode['number'] ?></strong></td>
                        <td class="episode-title"><?= $episode['title'] ? $episode['title'] : 'brak tytułu' ?></td>
                        <?php if($episode['enabled']): ?>
                            <td style="padding: 0" class="text-center"><span class="sprites <?php echo $episode['lang'] ?>"></span></td>
                            <td class="bg-<?= $episode['filler'] ? 'danger' : 'success' ?> text-center filler"><?php echo $episode['filler'] ? 'Tak' : 'Nie' ?></td>
                            <td class="text-center"><a href="./<?= $category->getAlias() ?>/<?php echo $episode['number'] ?>" class="btn btn-xs btn-success _visited">Zobacz</a></td>
                        <?php else: ?>
                            <td class="text-center">-</td>
                            <td class="text-center filler">-</td>
                            <td class="text-center">-</td>
                        <?php endif ?>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info fade in">
            <strong>Informacja!</strong> Brak odcinków!
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        </div>
    <?php endif ?>
</div>

<?= $view->render('Comments/index', array('type' => 0, 'to' => $category->getId())) ?>

<?php $view['slots']->start('_before_sidebar') ?>
    <div class="panel panel-default ratings">
        <div class="panel-body">
            <p class="row">
                    <span class="rating pull-left">
                        <i class="glyphicon glyphicon-star"></i><strong property="v:average" rel="v:rating"><?= $category->getRatingAvg() ?></strong><span property="v:best">/10</span><br>
                        <small><strong property="v:votes"><?= $category->getRatingCount() ?></strong> głosów</small>
                    </span>
                <span class="favorite pull-right"><a href="<?= $app->generateUrl('episodes_favorite', array('cat' => $category->getAlias())) ?>" class="fa fa-heart toggle-tooltip<?= $user_favorite ? ' active' : null ?>" title="<?= $user_favorite ? 'Usuń z ulubionych' : 'Dodaj do ulubionych' ?>" data-toggle="tooltip"></a></span>
                <span class="clearfix"></span>
            </p>
            <p class="text-center rating-bar row">
                <?php foreach($rating_title as $i => $v): ?>
                    <a href="<?= 2 == $category->getStatus() ? '#' : $app->generateUrl('episodes_rating', array('cat' => $category->getAlias(), 'value' => $i)) ?>" title="<?= $v ?> (<?= $i ?>)" class="fa fa-star-o<?= $i <= $user_rating ? ' active' : null ?>"></a>
                <?php endforeach ?>
            </p>
            <hr>
            <p class="row description">
                <?php if($app->getUser()->isUser()): ?>
                    <?php if($user_rating): ?>
                        <span class="pull-left"><i class="fa fa-star"></i>Oceniłeś na: <strong><?= $user_rating ?></strong>/10</span>
                        <span class="pull-right"><a href="<?= $app->generateUrl('episodes_rating', array('cat' => $category->getAlias(), 'value' => 'delete')) ?>" class="remove-rating">&times; usuń</a></span>
                        <span class="clearfix"></span>
                    <?php endif ?>
                    <?php if($user_favorite): ?>
                        <i class="fa fa-check"></i>Dodałeś do ulubionych<br>
                    <?php endif ?>
                <?php endif ?>
                <i class="fa fa-heart"></i><strong><?= $category->getFans() ?></strong> fanów<br>
                <i class="fa fa-eye"></i><strong><?= number_format($category->getViews()) ?></strong> wyświetleń
            </p>
        </div>
		<div class="panel-footer row watching-status">
            <a href="<?= 1 == $user_watching ? '#' :  $app->generateUrl('episodes_watched', array('cat' => $category->getAlias(), 'type' => 1)) ?>" class="btn btn-success btn-sm btn-block<?= 1 == $user_watching ? ' active' : null ?><?= $category->getStatus() == 2 ? ' disabled' : null ?>">
                <span class="pull-left"><i class="glyphicon glyphicon-play"></i> Oglądam</span>
                <span class="label label-transparent pull-right">
                    <?= $category->getWatching() ?><span class="mobile">&nbsp;osób ogląda</span>
                </span>
                <span class="clearfix"></span>
            </a>
            <a href="<?= 3 == $user_watching ? '#' : $app->generateUrl('episodes_watched', array('cat' => $category->getAlias(), 'type' => 3)) ?>" class="btn btn-warning btn-sm btn-block<?= 3 == $user_watching ? ' active' : null ?>">
                <span class="pull-left"><i class="glyphicon glyphicon-bookmark"></i> Planuję</span>
                <span class="label label-transparent pull-right">
                    <?= $category->getPlans() ?><span class="mobile">&nbsp;osób planuje obejrzeć</span>
                </span>
                <span class="clearfix"></span>
            </a>
            <a href="#" class="btn btn-inverse btn-sm btn-block">
                <span class="pull-left"><i class="glyphicon glyphicon-remove"></i> Usuń</span>
                <span class="clearfix"></span>
            </a>
        </div>
    </div>
<?php $view['slots']->stop() ?>