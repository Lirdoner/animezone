<?php $view->extend('content') ?>

<?php $view['slots']->set('_title', ($news->getTitle() ?: 'Nowości').' - '.$app->getConfig()->anime->get('title')) ?>
<?php $view['slots']->set('_description', $view['text']->truncate(strip_tags($news->getDescription()), 100, '...')) ?>
<?php $view['slots']->set('_sidebar', $view->render('Sidebar/faq', array('sidebar' => $sidebar))) ?>
<?php $view['slots']->start('_footer') ?>
    <script src="<?= $view['assets']->getUrl('javascript/jquery.autosize.min.js') ?>"></script>
    <script src="<?= $view['assets']->getUrl('javascript/comments.js') ?>"></script>
<?php $view['slots']->stop() ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="glyphicon glyphicon-list-alt"></i> <?= $news->getTitle() ?: $view['text']->timeElapsed($news->getDate(), 'date') ?></h3>
    </div>
    <div class="panel-body">
        <?= $view['text']->nl2p(($news->getImage() ? '<img src="'.$view['assets']->getUrl('kategorie/'.$news->getImage()).'" alt="" style="margin-right:10px;float:left">' : null).$news->getDescription()) ?>
    </div>
    <div class="panel-footer">
        <?php if($news->getTitle()): ?>
            <i class="fa fa-calendar"></i> <?= $view['text']->timeElapsed($news->getDate(), 'date') ?>&nbsp;
            <span class="desktop"><i class="fa fa-eye"></i> <?= number_format($news->getViews()) ?> wyświetleń&nbsp;</span>
        <?php endif ?>
        <?php foreach($tags as $tag): ?>
            <a href="<?= $app->generateUrl('news_tags', array('tagID' => $tag['id'])) ?>"><i class="fa fa-tag"></i> <?= $tag['name'] ?></a>&nbsp;
        <?php endforeach ?>
    </div>
</div>
<?php if($news->getComments()): ?>
    <?= $view->render('Comments/index', array('type' => 2, 'to' => $news->getId())) ?>
<?php else: ?>
    <div class="alert alert-info fade in">
        Komentarze pod tym newsem zostały wyłączone przez administratora.<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    </div>
<?php endif ?>