<div class="panel panel-default desktop">
    <div class="panel-heading">
        <h4 class="panel-title"><i class="fa fa-film"></i> Emitowane</h4>
    </div>
    <div class="panel-body list-group row list-group-striped">
        <?php foreach($sidebar as $i => $item): ?>
            <a href="./odcinki-online/<?= $item['alias'] ?>" class="list-group-item"><span class="caret"></span> <?= htmlspecialchars($item['name']) ?></a>
        <?php endforeach ?>
    </div>
</div>