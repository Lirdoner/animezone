<?php

!empty($name) ?: $name = false;
!empty($title) ?: $title = false;
!empty($number) ?: $number = false;
isset($filler) ?: $filler = null;
isset($enabled) ?: $enabled = null;

?>

<div class="panel panel-default desktop">
    <div class="panel-heading">
        <h4 class="panel-title"><i class="fa fa-search"></i> Szukaj</h4>
    </div>
    <div class="panel-body">
        <form action="<?= $app->generateUrl('episodes_search') ?>" method="post">
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
                <select class="form-control input-sm" name="filler">
                    <option disabled<?= is_null($filler) ? ' selected' : null ?>>Filler..</option>
                    <option value="1"<?= 1 === $filler ? ' selected' : null ?>>Tak</option>
                    <option value="2"<?= 0 === $filler ? ' selected' : null ?>>Nie</option>
                </select>
            </div>
            <div class="form-group">
                <select class="form-control input-sm" name="enabled">
                    <option disabled<?= is_null($enabled) ? ' selected' : null ?>>Status..</option>
                    <option value="1"<?= 1 === $enabled ? ' selected' : null ?>>Działające</option>
                    <option value="0"<?= 0 === $enabled ? ' selected' : null ?>>Uszkodzony</option>
                </select>
            </div>
            <button type="submit" class="btn btn-info btn-sm">Submit</button>
        </form>
    </div>
</div>