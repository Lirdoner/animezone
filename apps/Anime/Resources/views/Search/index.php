<?php $view->extend('content') ?>

<?php $view['slots']->set('_title', 'Wyszukiwarka - '.$app->getConfig()->anime->get('title')) ?>
<?php $view['slots']->set('_sidebar', $view->render('Sidebar/faq', array('sidebar' => $sidebar))) ?>

<div class="panel panel-default category-description">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="glyphicon glyphicon-search"></i> Wyszukiwarka</h3>
    </div>
    <div class="panel-body">
        <?php if(!empty($error)): ?>
            <div class="bs-callout bs-callout-danger">
                <p><?= $error ?></p>
            </div>
        <?php endif ?>
        <form role="form" method="post" action="<?= $app->generateUrl('search') ?>">
            <div class="input-group">
                <input type="text" name="query" value="<?= $query ?>" class="form-control" placeholder="szukana fraza" pattern=".{3,100}" required title="Minimum 3 znaki, maksimum 100 znaków.">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="submit"> Szukaj </button>
                </span>
            </div>
        </form>
    </div>
    <?php if(isset($result) && $result->rowCount() > 0): ?>
        <h5>Około <?= number_format($total_count) ?> wyników pasujących do zapytania.</h5>
        <div class="panel-body categories-newest">
            <?php foreach($result->fetchAll() as $row): ?>
                <div class="well well-sm categories">
                    <div class="image pull-left">
                        <a href="<?= $app->generateUrl('episodes_cat', array('cat' => $row['alias'])) ?>">
                            <img src="<?= $view['assets']->getUrl('kategorie/'.$row['image']) ?>" alt="" class="img-responsive lazy-loading" title="<?= $row['name'] ?>">
                        </a>
                    </div>
                    <div class="description pull-right">
                        <span class="label label-grey text-center">
                            <a href="<?= $app->generateUrl('episodes_cat', array('cat' => $row['alias'])) ?>">
                                <?= preg_replace('#('.preg_quote($query).')#i', '<mark>$1</mark>', $row['name']) ?>
                            </a>
                        </span>
                        <p><?= preg_replace('#('.preg_quote($query).')#i', '<mark>$1</mark>', $view['text']->truncate(strip_tags($row['description']), 270)) ?></p>
                    </div>
                    <div class="clearfix"></div>
                </div>
            <?php endforeach ?>
            <div class="text-center">
                <?= $pagination->getHtml() ?>
            </div>
        </div>
    <?php elseif(isset($result) && !$result->rowCount()): ?>
        <div class="alert alert-info fade in">
            Podana fraza nie została odnaleziona <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        </div>
    <?php endif ?>
</div>