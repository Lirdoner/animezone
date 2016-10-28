<?php !empty($current) ?: $current = false ?>
<div class="panel panel-default ratings hidden-md hidden-lg">
    <div class="panel-footer row watching-status">
        <a href="<?= $app->generateUrl('users_index') ?>" class="btn btn-default btn-sm btn-block<?php if('users' == $current): ?> active<?php endif ?>"><span class="pull-left"><i class="fa fa-users"></i> Użytkownicy</span><span class="clearfix"></span></a>
        <a href="<?= $app->generateUrl('sessions_index') ?>" class="btn btn-primary btn-sm btn-block<?php if('sessions' == $current): ?> active<?php endif ?>"><span class="pull-left"><i class="fa fa-database"></i> Aktywne sesje</span><span class="clearfix"></span></a>
        <a href="<?= $app->generateUrl('comments_index') ?>" class="btn btn-warning btn-sm btn-block<?php if('comments' == $current): ?> active<?php endif ?>"><span class="pull-left"><i class="fa fa-comments"></i> Komentarze</span><span class="clearfix"></span></a>
        <a href="<?= $app->generateUrl('reports_index') ?>" class="btn btn-danger btn-sm btn-block<?php if('reports' == $current): ?> active<?php endif ?>"><span class="pull-left"><i class="fa fa-bug"></i> Raporty</span><span class="clearfix"></span></a>
    </div>
</div>
<div class="panel panel-default desktop">
    <div class="panel-heading">
        <h4 class="panel-title"><i class="glyphicon glyphicon-list"></i> Kategorie</h4>
    </div>
    <div class="panel-body">
        <ul class="list-unstyled nav-stacked">
            <li><span class="caret"></span> <a href="<?= $app->generateUrl('users_index') ?>"<?php if('users' == $current): ?> class="underline"<?php endif ?>>Użytkownicy</a></li>
            <li><span class="caret"></span> <a href="<?= $app->generateUrl('sessions_index') ?>"<?php if('sessions' == $current): ?> class="underline"<?php endif ?>>Aktywne sesje</a></li>
            <li><span class="caret"></span> <a href="<?= $app->generateUrl('comments_index') ?>"<?php if('comments' == $current): ?> class="underline"<?php endif ?>>Komentarze</a></li>
            <li><span class="caret"></span> <a href="<?= $app->generateUrl('reports_index') ?>"<?php if('reports' == $current): ?> class="underline"<?php endif ?>>Raporty</a></li>
        </ul>
    </div>
</div>

<?php $view['slots']->output('_sidebar_addon') ?>