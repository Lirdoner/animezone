<?php

!empty($subject) ?: $subject = false;
!empty($mail) ?: $mail = false;
!empty($report_ip) ?: $report_ip = false;
isset($type) ?: $type = null;

?>

<div class="panel panel-default desktop">
    <div class="panel-heading">
        <h4 class="panel-title"><i class="fa fa-search"></i> Szukaj</h4>
    </div>
    <div class="panel-body">
        <form action="<?= $app->generateUrl('reports_search') ?>" method="post">
            <div class="form-group">
                <input type="text" class="form-control input-sm" placeholder="Tytuł.." autocomplete="off" name="subject"<?php if($subject): ?> value="<?= $subject ?>"<?php endif ?>>
            </div>
            <div class="form-group">
                <input type="text" class="form-control input-sm" placeholder="E-mail nadawcy.." autocomplete="off" name="mail"<?php if($mail): ?> value="<?= $mail ?>"<?php endif ?>>
            </div>
            <div class="form-group">
                <input type="text" class="form-control input-sm" placeholder="Adres IP.." autocomplete="off" name="report_ip"<?php if($report_ip): ?> value="<?= $report_ip ?>"<?php endif ?>>
            </div>
            <div class="form-group">
                <select class="form-control input-sm" name="type">
                    <option disabled<?= is_null($type) ? ' selected' : null ?>>Rodzaj..</option>
                    <option value="1"<?= 1 == $type ? ' selected' : null ?>>Błędny link</option>
                    <option value="2"<?= 2 == $type ? ' selected' : null ?>>Błędny komentarz</option>
                    <option value="3"<?= 3 == $type ? ' selected' : null ?>>Kontakt</option>
                </select>
            </div>
            <button type="submit" class="btn btn-info btn-sm">Submit</button>
        </form>
    </div>
</div>