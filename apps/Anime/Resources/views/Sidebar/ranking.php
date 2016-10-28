<?php if(!empty($sidebar)): ?>
    <div class="panel panel-transparent">
        <ul class="nav nav-pills nav-stacked">
            <?php foreach($sidebar as $name => $value): ?>
                <li<?= $name == $current ? ' class="active"' : null ?>><a href="<?= $rankingRoutes[$name] ?>">Ranking <?= $value ?></a></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif ?>