<div class="panel panel-default desktop">
    <div class="panel-heading">
        <h4 class="panel-title"><i class="fa fa-question-circle"></i> FAQ</h4>
    </div>
    <div class="panel-body list-group row list-group-striped">
        <?php foreach($sidebar as $i => $item): ?>
            <a href="<?= $app->generateUrl('faq') ?>" class="list-group-item" title="<?= htmlspecialchars($item['question']) ?>"><span class="caret"></span> <?= htmlspecialchars($item['question']) ?></a>
        <?php endforeach ?>
    </div>
</div>