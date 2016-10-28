<?php $view->extend('content') ?>

<?php $view['slots']->set('_title', 'Lista odcinków '.(!strcmp($type, 'lista') ? 'anime' : 'filmów').' - '.$app->getConfig()->anime->get('title')) ?>
<?php $view['slots']->set('_sidebar', $view->render('Sidebar/faq', array('sidebar' => $sidebar))) ?>

<div class="panel panel-default category-description">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="glyphicon glyphicon-film"></i> Lista <?= !strcmp($type, 'lista') ? 'anime' : 'filmów' ?></h3>
    </div>
    <div class="panel-body anime-list text-center">
        <div class="btn-group btn-group-xs">
            <?php foreach($letter_list as $item): ?>
                <a href="<?= $app->generateUrl('anime_list_by_type', array('type' => $type, 'letter' => $item)) ?>" class="btn <?= !strcmp($letter, $item) ? 'btn-primary active' : 'btn-default' ?>"><?= $item ?></a>
            <?php endforeach ?>
        </div>
    </div>
    <h5><?php echo !strcmp($type, 'lista') ? 'Anime' : 'Filmy' ?> na literę: <?= $letter ?></h5>
    <div class="panel-body categories-newest">
        <?php if($list->rowCount()): ?>
            <?php foreach($list->fetchAll() as $row): ?>
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
                        <p><?= $view['text']->truncate(strip_tags($row['description']), 270) ?></p>
                    </div>
                    <div class="clearfix"></div>
                </div>
            <?php endforeach ?>
            <div class="text-center">
                <?= $pagination->getHtml() ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info fade in">
                Brak anime do wyświetlenia.
            </div>
        <?php endif ?>
    </div>
</div>