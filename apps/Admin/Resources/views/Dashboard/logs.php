<?php $view->extend('layout') ?>

<?php $view['slots']->start('_footer') ?>
<script src="<?= $view['assets']->getUrl('javascript/jquery.shorten.js') ?>"></script>
<script src="<?= $view['assets']->getUrl('javascript/dashboard.js') ?>"></script>
<?php $view['slots']->stop() ?>

<style>
    .bg-error {
        background-color: #f2dede !important;
    }

    .bg-info {
        background-color: #d9edf7 !important;
    }

    .bg-info, .bg-error, pre {
        word-break: break-word;
    }
</style>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-bug"></i> Lista logów</h3>
        <a href="<?= $app->generateUrl('dashboard_logs', array('app' => $frontend == 'frontend' ? 'backend' : 'frontend')) ?>" class="btn btn-xs btn-primary pull-right btn-helper"><i class="fa fa-exchange"></i> <?= $frontend == 'backend' ? 'backend' : 'frontend' ?></a>
    </div>
    <?php if(!empty($logs)): ?>
        <table class="table table-bordered table-striped" style="margin-bottom: 0">
            <tbody>
            <?php foreach(array_reverse($logs) as $i => $log): ?>
                <tr class="log-row">
                    <td class="<?= 'EMERGENCY' == $log['level'] || 'CRITICAL' == $log['level'] ? 'bg-error' : 'bg-info' ?>">
                        <?= $log['date'] ? $log['date'].' - ' : null ?>
                        <?= $log['level'] ? '<kbd>'.$log['level'].'</kbd> - ' : null ?>
                        <?= $log['message'] ? $log['message'] : null ?>
                        <?php if($log['context']): ?><pre><?= print_r($log['context'], true) ?></pre><?php endif ?>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="panel-body">
            <div class="alert alert-info fade in" style="margin-bottom: 0">
                Brak pozycji do wyświetlenia.<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            </div>
        </div>
    <?php endif ?>
</div>