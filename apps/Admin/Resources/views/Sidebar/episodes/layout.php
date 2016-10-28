<?php !empty($current) ?: $current = false ?>
<div class="panel panel-default ratings hidden-md hidden-lg">
    <div class="panel-footer row watching-status">
        <a href="<?= $app->generateUrl('episodes_index') ?>" class="btn btn-default btn-sm btn-block<?php if('episodes' == $current): ?> active<?php endif ?>"><span class="pull-left"><i class="fa fa-youtube-play"></i> Odcinki</span><span class="clearfix"></span></a>
        <a href="<?= $app->generateUrl('links_index') ?>" class="btn btn-primary btn-sm btn-block<?php if('links' == $current): ?> active<?php endif ?>"><span class="pull-left"><i class="fa fa-link"></i> Linki</span><span class="clearfix"></span></a>
        <a href="<?= $app->generateUrl('servers_index') ?>" class="btn btn-warning btn-sm btn-block<?php if('servers' == $current): ?> active<?php endif ?>"><span class="pull-left"><i class="fa fa-tasks"></i> Serwery</span><span class="clearfix"></span></a>
        <a href="<?= $app->generateUrl('submitted_index') ?>" class="btn btn-danger btn-sm btn-block<?php if('submitted' == $current): ?> active<?php endif ?>"><span class="pull-left"><i class="fa fa-code-fork"></i> Poczekalnia</span><span class="clearfix"></span></a>
    </div>
</div>
<div class="panel panel-default desktop">
    <div class="panel-heading">
        <h4 class="panel-title"><i class="glyphicon glyphicon-list"></i> Kategorie</h4>
    </div>
    <div class="panel-body">
        <ul class="list-unstyled nav-stacked">
            <li><span class="caret"></span> <a href="<?= $app->generateUrl('episodes_index') ?>"<?php if('episodes' == $current): ?> class="underline"<?php endif ?>>Odcinki</a></li>
            <li><span class="caret"></span> <a href="<?= $app->generateUrl('links_index') ?>"<?php if('links' == $current): ?> class="underline"<?php endif ?>>Linki</a></li>
            <li><span class="caret"></span> <a href="<?= $app->generateUrl('servers_index') ?>"<?php if('servers' == $current): ?> class="underline"<?php endif ?>>Serwery</a></li>
            <li><span class="caret"></span> <a href="<?= $app->generateUrl('submitted_index') ?>"<?php if('submitted' == $current): ?> class="underline"<?php endif ?>>Poczekalnia</a></li>
        </ul>
        <?php if('links' == $current): ?>
            <hr style="margin: 5px 0">
            <a href="<?= $app->generateUrl('links_clear') ?>" class="btn btn-primary btn-xs btn-block">Wyczyść cache</a>
        <?php endif ?>
    </div>
</div>

<?php $view['slots']->output('_sidebar_addon') ?>