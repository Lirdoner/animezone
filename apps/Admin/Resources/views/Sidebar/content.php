<?php !empty($current) ?: $current = false ?>
<div class="panel panel-default ratings hidden-md hidden-lg">
    <div class="panel-footer row watching-status">
        <a href="<?= $app->generateUrl('news_index') ?>" class="btn btn-default btn-sm btn-block<?php if('news' == $current): ?> active<?php endif ?>"><span class="pull-left"><i class="fa fa-list-alt"></i> Newsy</span><span class="clearfix"></span></a>
        <a href="<?= $app->generateUrl('tags_index') ?>" class="btn btn-inverse btn-sm btn-block<?php if('tags' == $current): ?> active<?php endif ?>"><span class="pull-left"><i class="fa fa-tags"></i> Tagi newsów</span><span class="clearfix"></span></a>
        <a href="<?= $app->generateUrl('faq_index') ?>" class="btn btn-primary btn-sm btn-block<?php if('faq' == $current): ?> active<?php endif ?>"><span class="pull-left"><i class="fa fa-question-circle"></i> FAQ</span><span class="clearfix"></span></a>
        <a href="<?= $app->generateUrl('pages_index') ?>" class="btn btn-warning btn-sm btn-block<?php if('pages' == $current): ?> active<?php endif ?>"><span class="pull-left"><i class="fa fa-puzzle-piece"></i> Podstrony</span><span class="clearfix"></span></a>
        <a href="<?= $app->generateUrl('menu_index') ?>" class="btn btn-danger btn-sm btn-block<?php if('menu' == $current): ?> active<?php endif ?>"><span class="pull-left"><i class="fa fa-slack"></i> Menu</span><span class="clearfix"></span></a>
        <a href="<?= $app->generateUrl('ads_index') ?>" class="btn btn-success btn-sm btn-block<?php if('ads' == $current): ?> active<?php endif ?>"><span class="pull-left"><i class="fa fa-delicious"></i> Reklamy</span><span class="clearfix"></span></a>
    </div>
</div>
<div class="panel panel-default desktop">
    <div class="panel-heading">
        <h4 class="panel-title"><i class="glyphicon glyphicon-list"></i> Treść</h4>
    </div>
    <div class="panel-body">
        <ul class="list-unstyled nav-stacked">
            <li><span class="caret"></span> <a href="<?= $app->generateUrl('news_index') ?>"<?php if('news' == $current): ?> class="underline"<?php endif ?>>Newsy</a></li>
            <li><span class="caret"></span> <a href="<?= $app->generateUrl('tags_index') ?>"<?php if('tags' == $current): ?> class="underline"<?php endif ?>>Tagi newsów</a></li>
            <li><span class="caret"></span> <a href="<?= $app->generateUrl('faq_index') ?>"<?php if('faq' == $current): ?> class="underline"<?php endif ?>>FAQ</a></li>
            <li><span class="caret"></span> <a href="<?= $app->generateUrl('pages_index') ?>"<?php if('pages' == $current): ?> class="underline"<?php endif ?>>Podstrony</a></li>
            <li><span class="caret"></span> <a href="<?= $app->generateUrl('menu_index') ?>"<?php if('menu' == $current): ?> class="underline"<?php endif ?>>Menu</a></li>
            <li><span class="caret"></span> <a href="<?= $app->generateUrl('ads_index') ?>"<?php if('ads' == $current): ?> class="underline"<?php endif ?>>Reklamy</a></li>
        </ul>
    </div>
</div>