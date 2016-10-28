<?php $view->extend('layout') ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-database"></i> Backup</h3>
    </div>
    <?php if(!empty($files)): ?>
        <table class="table table-bordered table-striped table-hover" style="margin-bottom: 0">
            <thead>
            <tr>
                <th>Plik</th>
                <th>Data</th>
                <th class="text-center col-sm-1">Akcje</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($files as $file): ?>
                <tr>
                    <td class="col-sm-9"><?= $file['name'] ?></td>
                    <td class="text-center"><?= date_format(date_create('@'.$file['ctime']), 'Y-m-d H:i:s') ?></td>
                    <td class="text-center" style="padding: 8px 0 0 0">
                        <div class="btn-group btn-group-xs">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                Akcje <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu text-left" role="menu">
                                <li><a href="<?= $app->generateUrl('backup_download', array('fileName' => $file['name'])) ?>"><i class="fa fa-download"></i> Pobierz</a></li>
                                <li><a href="<?= $app->generateUrl('backup_delete', array('fileName' => $file['name'])) ?>"><i class="fa fa-times"></i> Usuń</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="panel-body">
            <div class="alert alert-info fade in" style="margin-bottom: 0">
                Brak pozycji do wyświetlenia.
            </div>
        </div>
    <?php endif ?>
</div>