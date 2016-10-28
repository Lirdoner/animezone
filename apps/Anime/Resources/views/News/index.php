<?php $view->extend('content') ?>

<?php $view['slots']->set('_title', 'Nowości - '.$app->getConfig()->anime->get('title')) ?>
<?php $view['slots']->set('_sidebar', $view->render('Sidebar/faq', array('sidebar' => $sidebar))) ?>

<?php foreach($latest->fetchAll() as $row): ?>
    <div class="panel panel-default" id="n<?= $row['id'] ?>">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="glyphicon glyphicon-list-alt"></i> <?= $row['title'] ?: $view['text']->timeElapsed($row['date'], 'date') ?></h3>
        </div>
        <div class="panel-body">
            <?= $view['text']->nl2p(($row['image'] ? '<img src="'.$view['assets']->getUrl('kategorie/'.$row['image']).'" alt="" style="margin-right:10px;float:left">' : null).$view['text']->truncate(strip_tags($row['description']), 600, '&hellip; <a href="'.$app->generateUrl('news_show', array('slug' => ($row['alias'] ?: $row['id']))).'">Czytaj dalej</a>')) ?>
        </div>
        <div class="panel-footer">
            <?php if(!empty($row['title'])): ?>
                <i class="fa fa-calendar"></i> <?= $view['text']->timeElapsed($row['date'], 'date') ?>&nbsp;
                <span class="desktop"><i class="fa fa-eye"></i> <?= number_format($row['views']) ?> wyświetleń&nbsp;</span>
            <?php endif ?>
            <?php foreach($tags->findForNews($row['id']) as $tag): ?>
                <a href="<?= $app->generateUrl('news_tags', array('tagID' => $tag['id'])) ?>"><i class="fa fa-tag"></i> <?= $tag['name'] ?></a>&nbsp;
            <?php endforeach ?>
            <?php if(!empty($row['comments'])): ?>
                <a href="<?= $app->generateUrl('news_show', array('slug' => ($row['alias'] ?: $row['id']))) ?>#comments" class="pull-right"><i class="fa fa-comments"></i> Komentarze</a>
            <?php endif ?>
        </div>
    </div>
<?php endforeach ?>

<div class="text-center">
    <?= $pagination ?>
</div>