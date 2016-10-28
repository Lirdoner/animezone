<?php $view->extend('content') ?>

<?php $view['slots']->set('_sidebar', $view->render('Sidebar/episodes/layout', array('current' => 'submitted'))) ?>

<?php

/** @var \Anime\Model\SubmittedEpisode\SubmittedEpisode $submitted */
//$submitted = null;

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-eye"></i> Podgląd</h3>
        <a href="<?= $app->generateUrl('submitted_index') ?>" class="btn btn-xs btn-danger pull-right btn-helper"><i class="fa fa-reply"></i>Wróć do listy</a>
    </div>
    <table class="table table-bordered table-striped">
        <tbody>
        <tr>
            <td class="col-md-3"><strong>ID Linku</strong></td>
            <td><?= $submitted->getId() ?></td>
        </tr>
        <tr>
            <td class="col-md-3"><strong>IP nadawcy</strong></td>
            <td><?= $submitted->getIp() ?></td>
        </tr>
        <tr>
            <td class="col-md-3"><strong>Data przesłania</strong></td>
            <td><?= $submitted->getDate() ?></td>
        </tr>
        <tr>
            <td colspan="2">
                <textarea class="form-control" rows="15" style="cursor:text" readonly><?= $submitted->getLinks() ?></textarea>
            </td>
        </tr>
        </tbody>
    </table>
</div>