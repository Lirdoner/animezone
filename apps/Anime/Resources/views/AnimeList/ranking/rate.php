<?php $view->extend('AnimeList/ranking/layout') ?>

<?php $view['slots']->set('_title', 'Ranking ocen - '.$app->getConfig()->anime->get('title')) ?>
<?php $view['slots']->set('_headline', 'ocen') ?>
<?php $view['slots']->set('_before_sidebar', $view->render('Sidebar/ranking', array('sidebar' => $sidebar, 'current' => 'ocen'))) ?>

    <div class="panel-body">
        <ul class="nav nav-pills nav-justified">
            <?php foreach($submenu as $key => $value): ?>
                <li<?= $key == $type ? ' class="active"' : null ?>><a href="<?php echo $app->generateUrl('anime_rate_ranking', array('type' => $key)) ?>"><?= $value ?></a></li>
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
            <th class="text-center<?= $repository->isCurrent('rating_avg') ? ' success' : null ?>">
                <a href="<?= $app->generateUrl('anime_rate_ranking', array('type' => $type, 'rating_avg' => $repository->getOrder('rating_avg'))) ?>">
                    <?= $repository->get('rating_avg')->getTitle() ?> <?php if($repository->isCurrent('rating_avg')): ?><span class="drop<?= $repository->getOrder('rating_avg') ?>"><span class="caret"></span></span><?php endif ?>
                </a>
            </th>
            <th class="text-center<?php echo $repository->isCurrent('rating_count') ? ' success' : null ?>">
                <a href="<?= $app->generateUrl('anime_rate_ranking', array('type' => $type, 'rating_count' => $repository->getOrder('rating_count'))) ?>">
                    <?= $repository->get('rating_count')->getTitle() ?> <?php if($repository->isCurrent('rating_count')): ?><span class="drop<?= $repository->getOrder('rating_count') ?>"><span class="caret"></span></span><?php endif ?>
                </a>
            </th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($ranking as $i => $row): ?>
            <tr>
                <td class="text-center"><?= $i+1 ?></td>
                <td><a href="<?= $app->generateUrl('episodes_cat', array('cat' => $row['alias'])) ?>"><?= htmlspecialchars($row['name']) ?></a></td>
                <td class="text-center"><?= $row['rating_avg'] ?></td>
                <td class="text-center"><?= number_format($row['rating_count']) ?></td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
<?php endif ?>