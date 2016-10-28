<?php $view->extend('content') ?>

<?php $view['slots']->set('_sidebar', $view->render('Sidebar/content', array('current' => 'faq'))) ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="glyphicon glyphicon-plus-sign"></i> Nowe pytanie</h3>
        <a href="<?= $app->generateUrl('faq_index') ?>" class="btn btn-xs btn-danger pull-right btn-helper"><i class="fa fa-reply"></i>Wróć do listy</a>
    </div>
    <div class="panel-body">
        <form class="form-horizontal" role="form" method="post" action="<?= $app->generateUrl('faq_create') ?>">
            <div class="form-group">
                <label for="_question" class="col-sm-2 control-label">Pytanie</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="faq[question]" id="_question" placeholder="Pytanie" pattern=".{1,200}" required title="Maksymalnie 200 znaków.">
                </div>
            </div>
            <div class="form-group">
                <label for="_answer" class="col-sm-2 control-label">Odpowiedź</label>
                <div class="col-sm-10">
                    <textarea class="form-control" name="faq[answer]" id="_answer" rows="9" required></textarea>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-default">Wyślij</button>
                </div>
            </div>
        </form>
    </div>
</div>