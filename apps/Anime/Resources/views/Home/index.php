<?php $view->extend('content') ?>

<?php $view['slots']->set('_sidebar', $view->render('Sidebar/emited', array('sidebar' => $sidebar))) ?>
<?php $view['slots']->set('_footer', '<script src="'.$view['assets']->getUrl('javascript/home.js').'"></script>') ?>

<?php if(!empty($news)): ?>
    <?php $view['slots']->start('_before_content') ?>
    <div class="news bs-callout bs-callout-info collapse fade in">
        <?php foreach($news as $news_li): ?>
			<h4>
				<small>[<?= $view['text']->timeElapsed($news_li['date'], 'date') ?>]</small> <a href="<?= $app->generateUrl('news_show', array('slug' => ($news_li['alias'] ?: $news_li['id']))) ?>"><?= $news_li['title'] ?: 'Aktualności' ?></a>
			</h4>
        <?php endforeach ?>
		<small><a href="<?= $app->generateUrl('news') ?>">Pokaż wszystkie newsy</a></small>
    </div>
    <?php $view['slots']->stop() ?>
<?php endif ?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="glyphicon glyphicon-tags"></i> Najnowsze odcinki</h3>
        <a href="<?= $app->generateUrl('homepage', array('lang' => $lang)) ?>" class="btn btn-xs btn-primary pull-right btn-helper">
            <i class="fa fa-language"></i>
            <?php if($lang): ?>
                <span class="desktop">Tylko odcinki po polsku</span><span class="mobile">PL</span>
            <?php else: ?>
                <span class="desktop">Wszystkie języki</span><span class="mobile">All</span>
            <?php endif ?>
        </a>
    </div>
    <div class="panel-body categories-collection">
        <?php foreach($latest_episodes as $episode): ?>
            <div class="well well-sm categories col-lg-12 col-md-12 col-sm-6 col-xs-12<?= $watching && $watching->has($episode['category_id']) ? ' watching' : null ?>">
                <a href="./odcinki-online/<?= $episode['alias'] ?>/<?php echo $episode['number'] ?>" class="image pull-left"><img src="./resources/kategorie/<?= $episode['image'] ?>" alt="" class="img-responsive lazy-loading"></a>
                <div class="info pull-right">
                    <p class="label label-grey text-center"><a href="./odcinki-online/<?= $episode['alias'] ?>"><?= htmlspecialchars($episode['name']) ?></a></p>
                    <p class="title">
                        <span class="mobile"><span class="sprites <?= $episode['lang'] ?>-xs"></span></span>
                        <a href="./odcinki-online/<?= $episode['alias'] ?>/<?= $episode['number'] ?>">Odcinek <?= $episode['number'] ?><span class="desktop">: <?= $episode['title'] ? $episode['title'] : 'brak tytułu' ?></span></a>
                    </p>
                    <p class="time"><small title="<?= $view['text']->timeElapsed($episode['date_add'], true) ?>"><i class="glyphicon glyphicon-time"></i><?= $view['text']->timeElapsed($episode['date_add']) ?></small></p>
                    <div class="sprites <?= $episode['lang'] ?> pull-right desktop"></div>
                </div>
            </div>
        <?php endforeach ?>
        <div class="text-center col-lg-12 col-sm-12 col-xs-12"><ul class="pagination"></ul></div>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="glyphicon glyphicon-folder-open"></i> Najnowsze serie</h3>
    </div>
    <div class="panel-body categories-newest">
        <?php foreach($newest_series as $series): ?>
            <div class="well well-sm categories">
                <div class="image pull-left">
                    <a href="./odcinki-online/<?= $series['alias'] ?>">
                        <img src="./resources/kategorie/<?= $series['image'] ?>" alt="" class="img-responsive lazy-loading" title="<?= $series['name'] ?>">
                    </a>
                </div>
                <div class="description pull-right">
                    <span class="label label-grey text-center">
                        <a href="./odcinki-online/<?= $series['alias'] ?>"><?php echo $series['name'] ?></a>
                    </span>
                    <p><?= $view['text']->truncate(strip_tags($series['description']), 294) ?></p>
                </div>
                <div class="clearfix"></div>
            </div>
        <?php endforeach ?>
    </div>
</div>