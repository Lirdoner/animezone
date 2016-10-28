<?php $view->extend('content') ?>

<?php $view['slots']->set('_sidebar_addon', $view->render('Sidebar/users/report_search')) ?>
<?php $view['slots']->set('_sidebar', $view->render('Sidebar/users/layout', array('current' => 'reports'))) ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-eye"></i> Podgląd raportu</h3>
        <div class="pull-right btn-helper">
            <a href="<?= $app->generateUrl('reports_index') ?>" class="btn btn-xs btn-primary"><i class="fa fa-reply"></i>Wróć do listy</a>
            <a href="<?= $app->generateUrl('reports_delete', array('reportID' => $report->getId())) ?>" class="btn btn-xs btn-danger"><i class="fa fa-times"></i> Usuń raport</a>
        </div>
    </div>
    <table class="table table-bordered table-striped">
        <tbody>
        <tr>
            <td class="col-md-2"><strong>ID raportu</strong></td>
            <td><?= $report->getId() ?></td>
        </tr>
        <tr>
            <td class="col-md-2"><strong>Rodzaj raportu</strong></td>
            <td>
                <?php if(1 == $report->getType()): ?>
                    Niepoprawny link
                <?php elseif(2 == $report->getType()): ?>
                    Niepoprawny komentarz
                <?php elseif(3 == $report->getType()): ?>
                    Kontakt
                <?php endif ?>
            </td>
        </tr>
        <tr>
            <td class="col-md-2"><strong>Data</strong></td>
            <td><?= $report->getDate() ?></td>
        </tr>
        <tr>
            <td class="col-md-2"><strong>IP użytkownika</strong></td>
            <td><a href="<?= $app->generateUrl('reports_search', array('report_ip' => $report->getReportIp())) ?>" target="_blank"><?= $report->getReportIp() ?> <i class="fa fa-external-link"></i></a></td>
        </tr>
        <?php if(1 == $report->getType()): ?>
            <tr>
                <td class="col-md-2"><strong>Link ID</strong></td>
                <td><?= $report->getLinkId() ?> <a href="<?= $app->generateUrl('links_delete', array('linkID' => $report->getLinkId())) ?>" class="btn btn-xs btn-danger"><i class="fa fa-times"></i>Usuń link</a></td>
            </tr>
            <tr>
                <td class="col-md-2"><strong>Server</strong></td>
                <td><?= $info['server'] ?></td>
            </tr>
            <tr>
                <td class="col-md-2"><strong>Język</strong></td>
                <td><?= $info['lang'] ?></td>
            </tr>
            <tr>
                <td class="col-md-2"><strong>Edycja</strong></td>
                <td><a href="<?= $app->generateUrl('links_edit', array('linkID' => $report->getLinkId())) ?>" target="_blank"><?= $app->generateUrl('links_edit', array('linkID' => $report->getLinkId())) ?> <i class="fa fa-external-link"></i></a></td>
            </tr>
            <tr>
                <td class="col-md-2"><strong>Link publiczny</strong></td>
                <td><a href="<?= $app->basePath('/odcinki-online/'.$info['alias'].'/'.$info['number']) ?>" target="_blank"><?= $app->basePath('/odcinki-online/'.$info['alias'].'/'.$info['number']) ?> <i class="fa fa-external-link"></i></a></td>
            </tr>
        <?php elseif(2 == $report->getType()): ?>
            <tr>
                <td class="col-md-2"><strong>Autor</strong></td>
                <td><a href="<?= $app->generateUrl('users_edit', array('userID' => $info['user'])) ?>" target="_blank"><?= $info['user'] ?> <i class="fa fa-external-link"></i></a></td>
            </tr>
            <tr>
                <td class="col-md-2"><strong>Treść</strong></td>
                <td><?= nl2br($info['comment']->getMessage()) ?></td>
            </tr>
            <tr>
                <td class="col-md-2"><strong>Edycja</strong></td>
                <td><a href="<?= $app->generateUrl('comments_edit', array('commentID' => $report->getLinkId())) ?>" target="_blank"><?= $app->generateUrl('comments_edit', array('commentID' => $report->getLinkId())) ?> <i class="fa fa-external-link"></i></a></td>
            </tr>
            <tr>
                <td class="col-md-2"><strong>Link publiczny</strong></td>
                <td>@<a href="<?= $app->basePath('/comments/redirect/'.$info['comment']->getId()) ?>" target="_blank"><?php if(!$info['comment']->getType()): ?>Anime<?php elseif(1 == $info['comment']->getType()): ?>Odcinek<?php elseif(2 == $info['comment']->getType()): ?>News<?php endif ?> <i class="fa fa-external-link"></i></a></td>
            </tr>
        <?php elseif(3 == $report->getType()): ?>
            <tr>
                <td class="col-md-2"><strong>Tytuł</strong></td>
                <td><?= $report->getSubject() ?></td>
            </tr>
            <tr>
                <td class="col-md-2"><strong>E-mail nadawcy</strong></td>
                <td><?= $report->getMail() ?></td>
            </tr>
            <tr>
                <td class="col-md-2"><strong>Treść</strong></td>
                <td><?= nl2br(htmlspecialchars($report->getContent())) ?></td>
            </tr>
            <tr>
                <td colspan="2">
                    <form method="post" action="<?= $app->generateUrl('reports_reply', array('reportID' => $report->getId())) ?>">
                        <div class="form-group">
                            <textarea class="form-control" name="message" rows="9" placeholder="Prześlij odpowiedź"></textarea>
                        </div>
                        <div class="form-group">
                            <button type="submit" id="submit" class="btn btn-default">Wyślij</button>
                        </div>
                    </form>
                </td>
            </tr>
        <?php endif ?>
        </tbody>
    </table>
</div>