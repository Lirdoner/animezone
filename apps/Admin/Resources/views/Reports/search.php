<?php $view->extend('content') ?>

<?php $view['slots']->set('_sidebar_addon', $view->render('Sidebar/users/report_search', array('subject' => $subject, 'mail' => $mail, 'report_ip' => $report_ip, 'type' => $type))) ?>
<?php $view['slots']->set('_sidebar', $view->render('Sidebar/users/layout', array('current' => 'reports'))) ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-bug"></i> Lista raportów <span class="badge"><?= $total ?></span></h3>
        <a href="<?= $app->generateUrl('reports_index') ?>" class="btn btn-xs btn-danger pull-right btn-helper"><i class="fa fa-reply"></i>Wróć do listy</a>
    </div>
    <?php if($list->rowCount()): ?>
        <form method="post" action="<?= $app->generateUrl('reports_update') ?>">
            <table class="table table-bordered table-striped table-hover" style="margin-bottom: 0">
                <thead>
                <tr>
                    <th colspan="2">Tytuł</th>
                    <th class="text-center" style="width: 20%">Data</th>
                    <th class="text-center col-sm-1">Akcje</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($list->fetchAll() as $row): ?>
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" name="delete[]" value="<?= $row['id'] ?>" class="checkbox-inline">
                        </td>
                        <td class="col-sm-8">
                            <?php if(1 == $row['type']): ?>
                                <a href="<?= $app->generateUrl('reports_view', array('reportID' => $row['id'])) ?>" target="_blank">Błędny link: <?= $row['link_id'] ?> <i class="fa fa-external-link"></i></a>
                            <?php elseif(2 == $row['type']): ?>
                                <a href="<?= $app->generateUrl('reports_view', array('reportID' => $row['id'])) ?>" target="_blank">Błędny komentarz: <?= $row['link_id'] ?> <i class="fa fa-external-link"></i></a>
                            <?php elseif(3 == $row['type']): ?>
                                <a href="<?= $app->generateUrl('reports_view', array('reportID' => $row['id'])) ?>"><?= $view['text']->truncate(htmlspecialchars($row['subject']), 70) ?></a>
                            <?php endif ?>
                        </td>
                        <td class="text-center"><?= $row['date'] ?></td>
                        <td class="text-center" style="padding: 8px 0 0 0">
                            <div class="btn-group btn-group-xs">
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                    Akcje <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu text-left" role="menu">
                                    <li><a href="<?= $app->generateUrl('reports_delete', array('reportID' => $row['id'])) ?>"><i class="fa fa-times"></i> Usuń</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
            <div class="panel-body">
                <div class="checkbox" style="margin-top: 0; margin-bottom: 0; margin-left: -2px">
                    <label><input type="checkbox" class="select-all"> Zaznacz wszystkie</label> <button type="submit" class="btn btn-xs btn-default">usuń</button>
                    <?= $pagination ?>
                </div>
            </div>
        </form>
    <?php else: ?>
        <div class="panel-body">
            <div class="alert alert-info fade in" style="margin-bottom: 0">
                Brak pozycji do wyświetlenia.<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            </div>
        </div>
    <?php endif ?>
</div>