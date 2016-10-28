<div class="panel panel-default desktop">
    <div class="panel-heading">
        <h4 class="panel-title"><i class="fa fa-film"></i> <?= $similar_title ?> anime</h4>
    </div>
    <div class="panel-body list-group row list-group-striped">
        <?php foreach($sidebar as $i => $item): ?>
            <a href="<?= $app->generateUrl('episodes_cat', array('cat' => $item['alias'])) ?>" class="list-group-item"><span class="caret"></span> <?= htmlspecialchars($item['name']) ?></a>
        <?php endforeach ?>
    </div>
</div>