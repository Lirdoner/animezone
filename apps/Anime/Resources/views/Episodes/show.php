<?php $view->extend('content') ?>

<?php $view['slots']->set('_title', $category->getName().($category->getRelease() > 1 ? ' - odcinek '.$episode->getNumber() : null).' - oglądaj anime online') ?>
<?php $view['slots']->set('_sidebar', $view->render('Sidebar/similar', array('sidebar' => $similar, 'similar_title' => $similar_title))) ?>
<?php $view['slots']->set('video_prefix', $video_prefix) ?>

<?php $view['slots']->start('_head') ?>

    <meta property="og:site_name" content="AnimeZone.pl">
    <meta property="og:title" content="<?= $category->getName().($category->getRelease() > 1 ? 'odcinek '.$episode->getNumber() : null).' - odcinki anime online' ?>">
    <meta property="og:description" content="<?= $category->getName().' online - '.$view['text']->truncate(strip_tags($category->getDescription()), 100) ?>">
    <meta property="og:type" content="tv_show">
    <meta property="og:url" content="<?=$app->generateUrl('episodes_cat', array('cat' => $category->getAlias()), true) ?>">
    <meta property="og:image" content="http://<?= $app->getRequest()->getHost().$view['assets']->getUrl('kategorie/'.$category->getImage()) ?>">

<?php $view['slots']->stop() ?>

<?php $view['slots']->start('_footer') ?>
    <script src="<?= $view['assets']->getUrl('javascript/jquery.autosize.min.js') ?>"></script>
    <script src="<?= $view['assets']->getUrl('javascript/episode.js') ?>"></script>
    <script src="<?= $view['assets']->getUrl('javascript/comments.js') ?>"></script>
<?php $view['slots']->stop() ?>

<div class="panel panel-default category-description">
    <div class="panel-heading">
        <h3 class="panel-title" title="<?= $category->getName() ?>"><i class="glyphicon glyphicon-tag"></i> <?= htmlspecialchars($category->getName()) ?></h3>
        <a href="#" class="desktop pull-right sprites light on" title="Wyłącz światło">
            <img src="<?= $app->generateUrl('security_image') ?>" width="1" height="1" style="position:absolute">
        </a>
    </div>
    <div id="episode">
        <div class="panel-body embed-container"></div>
        <h5></h5>
    </div>
    <div class="panel-body">
        <h3 style="margin: 5px 0"><?= $category->getRelease() > 1 ? 'Odcinek '.$episode->getNumber().': ' : null ?><?= $episode->getTitle() ?></h3>
    </div>
    <div class="panel-body">
        <ul class="pager" style="margin: 0">
            <?php if(isset($neighbours['prev'])): ?>
                <li class="previous"><a href="<?= $app->generateUrl('episodes_show', array('cat' => $category->getAlias(), 'id' => $neighbours['prev'])) ?>">&larr; Poprzedni</a></li>
            <?php endif ?>
            <?php if(isset($neighbours['next'])): ?>
                <li class="next"><a href="<?= $app->generateUrl('episodes_show', array('cat' => $category->getAlias(), 'id' => $neighbours['next'])) ?>">Następny &rarr;</a></li>
            <?php endif ?>
        </ul>
    </div>
    <h5>
        Linki <span class="label pull-right label-<?= $episode->getFiller() ? 'danger' : 'success' ?>">Filler: <?= $episode->getFiller() ? 'tak' : 'nie' ?></span>
        <span class="label label-info pull-right all-episodes"><a href="<?= $app->generateUrl('episodes_cat', array('cat' => $category->getAlias())) ?>" >Wszystkie odcinki</a></span>
    </h5>
    <table class="table table-bordered table-striped episode">
        <thead class="bg-success">
        <tr>
            <th class="text-center col-sm-8 col-xs-5">Serwer</th>
            <th class="text-center postscript">Dopisek</th>
            <th class="text-center">Język</th>
            <th class="text-center">Link</th>
            <th class="text-center desktop">Raportuj</th>
        </tr>
        </thead>
        <tbody>
        <?php if($links->rowCount()): ?>
            <?php foreach($links->fetchAll() as $row): ?>
                <tr>
                    <td><?php if($row['mobile']): ?><i class="fa fa-mobile" style="font-size:20px;line-height:1px;vertical-align:-2px;"></i> <?php endif ?><?= $row['name'] ?></td>
                    <td class="text-center postscript"><?= $row['info'] ? $row['info'] : ' - ' ?></td>
                    <td class="text-center"><span class="sprites <?= $row['lang'] ?> lang"></span></td>
                    <?php if($episode->getEnabled()): ?>
                        <td class="text-center">
                            <button class="btn btn-xs btn-success play" data-<?= $video_prefix ?>="<?php echo $row['id'].':'.md5($row['id'].$video_salt) ?>">
                                <i class="fa fa-play"></i> <?= $app->getUser()->isAdmin() ? $row['id'].':' : null ?>Odtwórz
                            </button>
                        </td>
                        <td class="text-center desktop episode-report"><button class="btn btn-xs btn-danger report" title="Zgłoś niedziałajacy link" data-report="<?= $row['id'] ?>"><i class="fa fa-warning"></i> Zgłoś</button></td>
                    <?php else: ?>
                        <td class="text-center"> - </td>
                        <td class="text-center desktop"> - </td>
                    <?php endif ?>
                </tr>
            <?php endforeach ?>
        <?php else: ?>
            <div class="alert alert-info fade in">
                Brak linków dla tego odcinka. <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            </div>
        <?php endif ?>
        </tbody>
    </table>
</div>
<?= $view->render('Comments/index', array('type' => 1, 'to' => $episode->getId())) ?>