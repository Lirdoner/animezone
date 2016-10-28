<?php

!empty($name) ?: $name = false;
!empty($title) ?: $title = false;
!empty($number) ?: $number = false;
isset($server_id) ?: $server_id = null;
isset($lang_id) ?: $lang_id = null;

?>

<div class="panel panel-default desktop">
    <div class="panel-heading">
        <h4 class="panel-title"><i class="fa fa-search"></i> Szukaj</h4>
    </div>
    <div class="panel-body">
        <form action="<?= $app->generateUrl('links_search') ?>" method="post">
            <div class="form-group">
                <input type="text" class="form-control input-sm" placeholder="Nazwa kategorii.." autocomplete="off" name="name"<?php if($name): ?> value="<?= $name ?>"<?php endif ?>>
            </div>
            <div class="form-group">
                <input type="text" class="form-control input-sm" placeholder="Tytuł odcinka.." autocomplete="off" name="title"<?php if($title): ?> value="<?= $title ?>"<?php endif ?>>
            </div>
            <div class="form-group">
                <input type="number" class="form-control input-sm" placeholder="Numer.." autocomplete="off" name="number"<?php if($number): ?> value="<?= $number ?>"<?php endif ?>>
            </div>
            <div class="form-group">
                <select class="form-control input-sm" name="server_id">
                    <option disabled<?= is_null($server_id) ? ' selected' : null ?>>Serwer..</option>
                    <?php foreach($servers as $row): ?>
                        <option value="<?= $row['id'] ?>"<?= $server_id == $row['id'] ? ' selected' : null ?>><?= $row['name'] ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="form-group">
                <select class="form-control input-sm" name="lang_id">
                    <option disabled<?= is_null($lang_id) ? ' selected' : null ?>>Język..</option>
                    <?php foreach($languages as $id => $value): ?>
                        <option value="<?= $id ?>"<?= $lang_id == $id && !is_null($lang_id) ? ' selected' : null ?>><?= $value ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <button type="submit" class="btn btn-info btn-sm">Submit</button>
        </form>
    </div>
</div>