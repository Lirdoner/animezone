<?php $view->extend('AnimeList/ranking/layout') ?>

<?php $view['slots']->set('_title', 'Ranking wyświetleń - '.$app->getConfig()->anime->get('title')) ?>
<?php $view['slots']->set('_headline', 'wyświetleń') ?>
<?php $view['slots']->set('_before_sidebar', $view->render('Sidebar/ranking', array('sidebar' => $sidebar, 'current' => 'wyswietlen'))) ?>

    <div class="panel-body">
        <ul class="nav nav-pills nav-justified">
            <?php foreach($submenu as $key => $value): ?>
                <li<?= $key == $type ? ' class="active"' : null ?>><a href="<?= $app->generateUrl('anime_views_ranking', array('type' => $key)) ?>"><?= $value ?></a></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php if(empty($ranking)): ?>
    <div class="panel-body">
        <div class="alert alert-info fade in">
            Brak wyników <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        </div>
    </div>
<?php else: ?>
    <table class="table table-bordered table-striped table-hover ranking">
        <thead>
        <tr>
            <th class="text-center">#</th>
            <th class="col-sm-8">Anime</th>
            <th class="text-center success">
                <a href="<?= $app->generateUrl('anime_views_ranking', array('type' => $type, 'views' => $repository->getOrder('views'))) ?>">
                    <?= $repository->get('views')->getTitle() ?> <?php if($repository->isCurrent('views')): ?><span class="drop<?= $repository->getOrder('views') ?>"><span class="caret"></span></span><?php endif ?>
                </a>
            </th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($ranking as $i => $row): ?>
            <tr>
                <td class="text-center"><?= $i+1 ?></td>
                <td><a href="<?= $app->generateUrl('episodes_cat', array('cat' => $row['alias'])) ?>"><?= htmlspecialchars($row['name']) ?></a></td>
                <td class="text-center"><?= number_format($row['views']) ?></td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
<?php endif ?>