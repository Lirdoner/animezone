<?php $view->extend('content') ?>

<?php $view['slots']->set('_title', 'Sezon '.$year.' - '.$season.' - '.$app->getConfig()->anime->get('title')) ?>
<?php $view['slots']->set('_sidebar', $view->render('Sidebar/faq', array('sidebar' => $sidebar))) ?>

<div class="panel panel-default category-description">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="glyphicon glyphicon-film"></i> <?= $year.' - '.$season ?></h3>
    </div>
    <div class="panel-body">
        <ul class="pager" style="margin: 0">
            <?php if(false !== $nav['prev']['year']): ?>
                <li class="previous"><a href="<?= $app->generateUrl('anime_season', array('year' => $nav['prev']['year'], 'season' => $nav['prev']['season'])) ?>">&larr; Poprzedni</a></li>
            <?php else: ?>
                <li class="previous disabled"><a href="#">&larr; Poprzedni</a></li>
            <?php endif ?>
            <?php if(false !== $nav['next']['year']): ?>
                <li class="next"><a href="<?= $app->generateUrl('anime_season', array('year' => $nav['next']['year'], 'season' => $nav['next']['season'])) ?>">Następny &rarr;</a></li>
            <?php else: ?>
                <li class="next disabled"><a href="#">Następny &rarr;</a></li>
            <?php endif ?>
        </ul>
    </div>
    <div class="panel-body categories-newest">
        <?php if(!empty($list)): ?>
            <?php foreach($list as $row): ?>
                <div class="well well-sm categories">
                    <div class="image pull-left">
                        <a href="<?= $app->generateUrl('episodes_cat', array('cat' => $row['alias'])) ?>">
                            <img src="<?= $view['assets']->getUrl('kategorie/'.$row['image']) ?>" alt="" class="img-responsive lazy-loading" title="<?= $row['name'] ?>">
                        </a>
                    </div>
                    <div class="description pull-right">
                        <span class="label label-grey text-center">
                            <a href="<?= $app->generateUrl('episodes_cat', array('cat' => $row['alias'])) ?>"><?= $row['name'] ?></a>
                        </span>
                        <p><?= $view['text']->truncate(strip_tags($row['description']), 250) ?></p>
                    </div>
                    <div class="clearfix"></div>
                </div>
            <?php endforeach ?>
        <?php else: ?>
            <div class="alert alert-info fade in">
                Brak anime do wyświetlenia
            </div>
        <?php endif ?>
    </div>
</div>