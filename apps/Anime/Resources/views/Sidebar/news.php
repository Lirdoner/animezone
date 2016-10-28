<div class="panel panel-default desktop">
    <div class="panel-heading">
        <h4 class="panel-title"><i class="glyphicon glyphicon-list-alt"></i> Ostatnie newsy</h4>
    </div>
    <div class="panel-body list-group row list-group-striped">
        <?php foreach($sidebar as $i => $item): ?>
            <a href="<?= $app->generateUrl('news') ?>#n<?= $item['id'] ?>" class="list-group-item" title="<?= htmlspecialchars($view['text']->truncate($item['description'])) ?>">
                <span class="caret"></span> <?= htmlspecialchars($view['text']->truncate($item['description'], 35)) ?>
            </a>
        <?php endforeach ?>
    </div>
</div>