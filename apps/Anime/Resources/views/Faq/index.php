<?php $view->extend('content') ?>

<?php $view['slots']->set('_title', 'FAQ - '.$app->getConfig()->anime->get('title')) ?>
<?php $view['slots']->set('_sidebar', $view->render('Sidebar/news', array('sidebar' => $sidebar))) ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="glyphicon glyphicon-question-sign"></i> FAQ</h3>
    </div>
    <div class="panel-body" id="faq">
        <?php foreach($list as $i => $row): ?>
        <h4><a data-toggle="collapse" data-parent="#faq" href="#q<?= $row['id'] ?>"><i class="glyphicon glyphicon-file"></i><?= $row['question'] ?></a></h4>
        <div id="q<?= $row['id'] ?>" class="bs-callout bs-callout-info collapse<?php echo !$i ? ' in' : null ?>">

            <?= $view['text']->nl2p($row['answer']) ?>
        </div>
        <?php endforeach ?>
    </div>
</div>