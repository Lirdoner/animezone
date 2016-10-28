<?php !empty($current) ?: $current = false ?>
<div class="panel panel-default ratings hidden-md hidden-lg">
    <div class="panel-footer row watching-status">
        <a href="<?= $app->generateUrl('categories_index') ?>" class="btn btn-default btn-sm btn-block<?php if('categories' == $current): ?> active<?php endif ?>"><span class="pull-left"><i class="glyphicon glyphicon-film"></i> Kategorie</span><span class="clearfix"></span></a>
        <a href="<?= $app->generateUrl('species_index') ?>" class="btn btn-primary btn-sm btn-block<?php if('species' == $current): ?> active<?php endif ?>"><span class="pull-left"><i class="fa fa-briefcase"></i> Gatunki</span><span class="clearfix"></span></a>
        <a href="<?= $app->generateUrl('topics_index') ?>" class="btn btn-warning btn-sm btn-block<?php if('topics' == $current): ?> active<?php endif ?>"><span class="pull-left"><i class="fa fa-book"></i> Tematyki</span><span class="clearfix"></span></a>
        <a href="<?= $app->generateUrl('types_index') ?>" class="btn btn-danger btn-sm btn-block<?php if('types' == $current): ?> active<?php endif ?>"><span class="pull-left"><i class="fa fa-bookmark"></i> Typy widowni</span><span class="clearfix"></span></a>
        <a href="<?= $app->generateUrl('series_index') ?>" class="btn btn-success btn-sm btn-block<?php if('series' == $current): ?> active<?php endif ?>"><span class="pull-left"><i class="fa fa-stumbleupon"></i> Powiązane anime</span><span class="clearfix"></span></a>
    </div>
</div>
<div class="panel panel-default desktop">
    <div class="panel-heading">
        <h4 class="panel-title"><i class="glyphicon glyphicon-list"></i> Kategorie</h4>
    </div>
    <div class="panel-body">
        <ul class="list-unstyled nav-stacked">
            <li><span class="caret"></span> <a href="<?= $app->generateUrl('categories_index') ?>"<?php if('categories' == $current): ?> class="underline"<?php endif ?>>Kategorie</a></li>
            <li><span class="caret"></span> <a href="<?= $app->generateUrl('species_index') ?>"<?php if('species' == $current): ?> class="underline"<?php endif ?>>Gatunki</a></li>
            <li><span class="caret"></span> <a href="<?= $app->generateUrl('topics_index') ?>"<?php if('topics' == $current): ?> class="underline"<?php endif ?>>Tematyka</a></li>
            <li><span class="caret"></span> <a href="<?= $app->generateUrl('types_index') ?>"<?php if('types' == $current): ?> class="underline"<?php endif ?>>Typ widowni</a></li>
            <li><span class="caret"></span> <a href="<?= $app->generateUrl('series_index') ?>"<?php if('series' == $current): ?> class="underline"<?php endif ?>>Powiązane anime</a></li>
        </ul>
    </div>
</div>

<?php $view['slots']->output('_sidebar_addon') ?>