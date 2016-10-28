<?php

!empty($message) ?: $message = false;
isset($type) ?: $type = null;

?>

<div class="panel panel-default desktop">
    <div class="panel-heading">
        <h4 class="panel-title"><i class="fa fa-search"></i> Szukaj</h4>
    </div>
    <div class="panel-body">
        <form action="<?= $app->generateUrl('comments_search') ?>" method="post">
            <div class="form-group">
                <input type="text" class="form-control input-sm" placeholder="Treść.." autocomplete="off" name="message"<?php if($message): ?> value="<?= $message ?>"<?php endif ?>>
            </div>
            <div class="form-group">
                <select class="form-control input-sm" name="type">
                    <option disabled<?= is_null($type) ? ' selected' : null ?>>Rodzaj..</option>
                    <option value="0"<?= !is_null($type) && 0 == $type ? ' selected' : null ?>>Anime</option>
                    <option value="1"<?= 1 == $type ? ' selected' : null ?>>Odcinek</option>
                    <option value="2"<?= 2 == $type ? ' selected' : null ?>>News</option>
                </select>
            </div>
            <button type="submit" class="btn btn-info btn-sm">Submit</button>
        </form>
    </div>
</div>