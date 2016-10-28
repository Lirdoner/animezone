<?php

!empty($name) ?: $name = false;
!empty($letter) ?: $letter = false;
!empty($year) ?: $year = false;
isset($status) ?: $status = null;

?>

<div class="panel panel-default desktop">
    <div class="panel-heading">
        <h4 class="panel-title"><i class="fa fa-search"></i> Szukaj</h4>
    </div>
    <div class="panel-body">
        <form action="<?= $app->generateUrl('categories_search') ?>" method="post">
            <div class="form-group">
                <input type="text" class="form-control input-sm" placeholder="Nazwa.." name="name"<?php if($name): ?> value="<?= $name ?>"<?php endif ?>>
            </div>
            <div class="form-group">
                <input type="text" class="form-control input-sm" placeholder="Litera.." name="letter"<?php if($letter): ?> value="<?= $letter ?>"<?php endif ?>>
            </div>
            <div class="form-group">
                <input type="number" class="form-control input-sm" placeholder="Rok.." name="year"<?php if($year): ?> value="<?= $year ?>"<?php endif ?>>
            </div>
            <div class="form-group">
                <select class="form-control input-sm" name="status">
                    <option disabled<?= null === $status ? ' selected' : null ?>>Status..</option>
                    <?php foreach($_status as $i => $v): ?>
                        <option value="<?= $i ?>"<?= $status == $i && !is_null($status) ? ' selected' : null ?>><?= $v ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <button type="submit" class="btn btn-info btn-sm">Submit</button>
        </form>
    </div>
</div>