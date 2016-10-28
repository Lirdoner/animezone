<?php $view->extend('AnimeList/ranking/layout') ?>

<?php $view['slots']->set('_title', 'Ranking '.$watching[$watch]['nav'].' - '.$app->getConfig()->anime->get('title')) ?>
<?php $view['slots']->set('_headline', $watching[$watch]['nav']) ?>
<?php $view['slots']->set('_before_sidebar', $view->render('Sidebar/ranking', array('sidebar' => $sidebar, 'current' => $type))) ?>

<div class="panel-body">
    <ul class="nav nav-pills nav-justified">
        <?php foreach($watching as $key => $value): ?>
            <li<?= $key == $watch ? ' class="active"' : null ?>><a href="<?= $app->generateUrl('anime_watch_ranking', array('type' => $type, 'watch' => $key)) ?>"><?= $value['nav'] ?></a></li>
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
            <th class="text-center<?= $repository->isCurrent($watch) ? ' success' : null ?>">
                <a href="<?= $app->generateUrl('anime_watch_ranking', array('type' => $type, 'watch' => $watch, $watch => $repository->getOrder($watch))) ?>">
                    <?= $repository->get($watch)->getTitle() ?> <?php if($repository->isCurrent($watch)): ?><span class="drop<?= $repository->getOrder($watch) ?>"><span class="caret"></span></span><?php endif ?>
                </a>
            </th>
            <th class="text-center<?= $repository->isCurrent('views') ? ' success' : null ?>">
                <a href="<?= $app->generateUrl('anime_watch_ranking', array('type' => $type, 'watch' => $watch, 'views' => $repository->getOrder('views'))) ?>">
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
                <td class="text-center"><?= number_format($row[$watch]) ?></td>
                <td class="text-center"><?= number_format($row['views']) ?></td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
<?php endif ?>