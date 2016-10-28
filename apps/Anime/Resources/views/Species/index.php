<?php $view->extend('content') ?>

<?php $view['slots']->set('_title', 'Lista gatunków anime - '.$app->getConfig()->anime->get('title')) ?>
<?php $view['slots']->set('_sidebar', $view->render('Sidebar/faq', array('sidebar' => $sidebar))) ?>

<?php $view['slots']->start('_footer') ?>
    <script>
        $('.reset').hide();

        $('input:radio').change(function() {
            $('.reset').show();
        });

        if($('input').is(':checked'))
        {
            $('.reset').show();
        }

        var vars = [], hash;
        var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
        for(var i = 0; i < hashes.length; i++)
        {
            hash = hashes[i].split('=');
            vars.push(hash[0]);
            vars[hash[0]] = hash[1];
        }

        if("type" in vars || "species" in vars || "topic" in vars)
        {
            $("html, body").animate({
                scrollTop: $("#result").offset().top - 100
            }, 300);
        }
    </script>
<?php $view['slots']->stop() ?>

<div class="panel panel-default category-description">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="glyphicon glyphicon-tags"></i> Gatunki</h3>
    </div>
    <div class="panel-body">
        <div class="bs-callout bs-callout-info" style="margin: 5px 0;">
            <p>
                Z poniższej listy możesz zaznaczyć od jednej do trzech podkatgorii. W przypadku kiedy chciałbyś zresetować zaznaczone kategorie
                (np nie chcesz już szukać według tematyki) możesz użyć przycisku "reset".
            </p>
            <p>
                Wynik możesz sortować, klikając w przyciski tuż nad listą znalezionych anime. Wskaźnik <span class="dropdown"><span class="caret"></span></span> oznacza sortowanie od najwyższego,
                odpowiednio <span class="dropup"><span class="caret"></span></span> oznacza sortowanie od najniższego wyniku. Niebieski przycisk oznacza aktywne sortowanie.
            </p>
        </div>
    </div>
    <form action="<?= $app->generateUrl('anime_species') ?>" class="species">
        <h5>Typ widowni</h5>
        <div class="panel-body">
            <?php foreach($types as $row): ?>
                <div class="col-sm-4 col-xs-6">
                    <label>
                        <input type="radio" name="type" value="<?= $row['id'] ?>"<?= $current_type == $row['id'] ? ' checked="true"' : null ?>> <?= $row['name'] ?>
                    </label>
                </div>
            <?php endforeach ?>
        </div>
        <h5>Gatunek</h5>
        <div class="panel-body">
            <?php foreach($species as $row): ?>
                <div class="col-sm-4 col-xs-6">
                    <label>
                        <input type="radio" name="species" value="<?= $row['id'] ?>"<?= $current_species == $row['id'] ? ' checked="true"' : null ?>> <?= $row['name'] ?>
                    </label>
                </div>
            <?php endforeach ?>
        </div>
        <h5>Tematyka</h5>
        <div class="panel-body">
            <?php foreach($topics as $row): ?>
                <div class="col-sm-4 col-xs-6">
                    <label>
                        <input type="radio" name="topic" value="<?php echo $row['id'] ?>"<?= $current_topic == $row['id'] ? ' checked="true"' : null ?>> <?= $row['name'] ?>
                    </label>
                </div>
            <?php endforeach ?>
        </div>
        <div class="panel-body">
            <button type="submit" class="btn btn-primary"><i class="glyphicon glyphicon-search"></i> Szukaj</button>
            <a href="<?= $app->generateUrl('anime_species') ?>" class="btn btn-primary reset"><i class="glyphicon glyphicon-repeat"></i> Resetuj</a>
        </div>
    </form>
    <?php if(isset($result)): ?>
        <?php if($result->rowCount()): ?>
            <h5>Około <?= number_format($total_count) ?> wyników pasujących do kryteriów wyszukiwania.</h5>
            <div class="panel-body text-right" style="padding-bottom: 0;" id="result">
                <a href="<?= $app->generateUrl('anime_species', array('type' => $current_type, 'species' => $current_species, 'topic' => $current_topic, 'name' => $repository->getOrder('name'))) ?>" class="btn btn-xs<?= $repository->isCurrent('name') ? ' btn-primary' : ' btn-default' ?>">
                    Nazwa <?php if($repository->isCurrent('name')): ?><span class="drop<?= $repository->getOrder('name') ?>"><span class="caret"></span></span><?php endif ?>
                </a>
                <a href="<?= $app->generateUrl('anime_species', array('type' => $current_type, 'species' => $current_species, 'topic' => $current_topic, 'rating_avg' => $repository->getOrder('rating_avg'))) ?>" class="btn btn-xs<?= $repository->isCurrent('rating_avg') ? ' btn-primary' : ' btn-default' ?>">
                    Ocena <?php if($repository->isCurrent('rating_avg')): ?><span class="drop<?= $repository->getOrder('rating_avg') ?>"><span class="caret"></span></span><?php endif ?>
                </a>
                <a href="<?= $app->generateUrl('anime_species', array('type' => $current_type, 'species' => $current_species, 'topic' => $current_topic, 'fans' => $repository->getOrder('fans'))) ?>" class="btn btn-xs<?= $repository->isCurrent('fans') ? ' btn-primary' : ' btn-default' ?>">
                    Fanów <?php if($repository->isCurrent('fans')): ?><span class="drop<?= $repository->getOrder('fans') ?>"><span class="caret"></span></span><?php endif ?>
                </a>
                <a href="<?= $app->generateUrl('anime_species', array('type' => $current_type, 'species' => $current_species, 'topic' => $current_topic, 'views' => $repository->getOrder('views'))) ?>" class="btn btn-xs<?= $repository->isCurrent('views') ? ' btn-primary' : ' btn-default' ?>">
                    Wyświetleń <?php if($repository->isCurrent('views')): ?><span class="drop<?= $repository->getOrder('views') ?>"><span class="caret"></span></span><?php endif ?>
                </a>
            </div>
            <div class="panel-body categories-newest">
                <?php foreach($result->fetchAll() as $row): ?>
                    <div class="well well-sm categories">
                        <div class="image pull-left">
                            <a href="<?= $app->generateUrl('episodes_cat', array('cat' => $row['alias'])) ?>">
                                <img src="<?= $view['assets']->getUrl('kategorie/'.$row['image']) ?>" alt="" class="img-responsive lazy-loading" title="<?= $row['name'] ?>">
                            </a>
                            <div class="btn-group btn-group-justified" style="clear: both;width: 100%">
                                <span class="btn btn-xs" title="Średnia ocena"><i class="fa fa-star"></i><?= $row['rating_avg'] ?></span>
                                <span class="btn btn-xs" title="Ilość fanów"><i class="fa fa-heart"></i><?= number_format($row['fans']) ?></span>
                                <span class="btn btn-xs" title="Ilość wyświetleń"><i class="fa fa-eye"></i><?= number_format($row['views']) ?></span>
                            </div>
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
            </div>
        <?php else: ?>
            <div class="alert alert-info fade in">
                Brak wyników spełniających powyższe wyszukiwania <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            </div>
        <?php endif ?>
    <?php endif ?>
</div>