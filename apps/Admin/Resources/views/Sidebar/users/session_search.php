<?php

!empty($name) ?: $name = false;
!empty($user_ip) ?: $user_ip = false;
!empty($user_agent) ?: $user_agent = false;
isset($user_role) ?: $user_role = null;

?>

<div class="panel panel-default desktop">
    <div class="panel-heading">
        <h4 class="panel-title"><i class="fa fa-search"></i> Szukaj</h4>
    </div>
    <div class="panel-body">
        <form action="<?= $app->generateUrl('sessions_search') ?>" method="post">
            <div class="form-group">
                <input type="text" class="form-control input-sm" placeholder="Nazwa użytkownika.." autocomplete="off" name="name"<?php if($name): ?> value="<?= $name ?>"<?php endif ?>>
            </div>
            <div class="form-group">
                <input type="text" class="form-control input-sm" placeholder="Adres IP.." autocomplete="off" name="user_ip"<?php if($user_ip): ?> value="<?= $user_ip ?>"<?php endif ?>>
            </div>
            <div class="form-group">
                <input type="text" class="form-control input-sm" placeholder="User agent.." autocomplete="off" name="user_agent"<?php if($user_agent): ?> value="<?= $user_agent ?>"<?php endif ?>>
            </div>
            <div class="form-group">
                <select class="form-control input-sm" name="user_role">
                    <option disabled<?= is_null($user_role) ? ' selected' : null ?>>Uprawnienia..</option>
                    <option value="ROLE_GUEST"<?= 'ROLE_GUEST' == $user_role ? ' selected' : null ?>>Gość</option>
                    <option value="ROLE_USER"<?= 'ROLE_USER' == $user_role ? ' selected' : null ?>>Użytkownik</option>
                    <option value="ROLE_ADMIN"<?= 'ROLE_ADMIN' == $user_role ? ' selected' : null ?>>Administrator</option>
                </select>
            </div>
            <button type="submit" class="btn btn-info btn-sm">Submit</button>
        </form>
    </div>
</div>