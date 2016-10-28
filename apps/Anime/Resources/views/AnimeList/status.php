<?php $view->extend('content') ?>

<?php $view['slots']->set('_title', $title.' serie anime - '.$app->getConfig()->anime->get('title')) ?>
<?php $view['slots']->set('_sidebar', $view->render('Sidebar/faq', array('sidebar' => $sidebar))) ?>

<div class="panel panel-default category-description">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="glyphicon glyphicon-film"></i> <?= $title ?> serie</h3>
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
                            <a href="<?= $app->generateUrl('episodes_cat', array('cat' => $row['alias'])) ?>"><?php echo $row['name'] ?></a>
                        </span>
                        <p><?= $view['text']->truncate(strip_tags($row['description']), 250) ?></p>
                    </div>
                    <div class="clearfix"></div>
                </div>
            <?php endforeach ?>
        <?php else: ?>
            <div class="alert alert-info fade in">
                Brak anime do wyświetlenia <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            </div>
        <?php endif ?>
    </div>
</div>