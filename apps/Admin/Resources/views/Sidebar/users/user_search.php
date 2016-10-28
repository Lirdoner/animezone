<?php



!empty($name) ?: $name = false;

!empty($email) ?: $email = false;

!empty($ip) ?: $ip = false;

!empty($location) ?: $location = false;

isset($gender) ?: $gender = null;

isset($enabled) ?: $enabled = null;

isset($role) ?: $role = null;

isset($last_login) ?: $last_login = null;



?>



<div class="panel panel-default desktop">

    <div class="panel-heading">

        <h4 class="panel-title"><i class="fa fa-search"></i> Szukaj</h4>

    </div>

    <div class="panel-body">

        <form action="<?= $app->generateUrl('users_search') ?>" method="post">

            <div class="form-group">

                <input type="text" class="form-control input-sm" placeholder="Nazwa.." autocomplete="off" name="name"<?php if($name): ?> value="<?= $name ?>"<?php endif ?>>

            </div>

            <div class="form-group">

                <input type="text" class="form-control input-sm" placeholder="E-mail.." autocomplete="off" name="email"<?php if($email): ?> value="<?= $email ?>"<?php endif ?>>

            </div>

            <div class="form-group">

                <input type="text" class="form-control input-sm" placeholder="IP.." autocomplete="off" name="ip"<?php if($ip): ?> value="<?= $ip ?>"<?php endif ?>>

            </div>

            <div class="form-group">

                <input type="text" class="form-control input-sm" placeholder="Lokalizacja.." autocomplete="off" name="location"<?php if($location): ?> value="<?= $location ?>"<?php endif ?>>

            </div>

            <div class="form-group">

                <select class="form-control input-sm" name="gender">

                    <option disabled<?= is_null($gender) ? ' selected' : null ?>>Płeć..</option>

                    <option value="1"<?= 1 == $gender ? ' selected' : null ?>>Mężczyzna</option>

                    <option value="2"<?= 2 == $gender ? ' selected' : null ?>>Kobieta</option>

                </select>

            </div>

            <div class="form-group">

                <select class="form-control input-sm" name="enabled">

                    <option disabled<?= is_null($enabled) ? ' selected' : null ?>>Status..</option>

                    <option value="0"<?= 0 == $enabled && null !== $enabled ? ' selected' : null ?>>Nieaktywny</option>

                    <option value="1"<?= 1 == $enabled ? ' selected' : null ?>>Aktywny</option>

                    <option value="2"<?= 2 == $enabled ? ' selected' : null ?>>Zablokowany</option>

                </select>

            </div>

            <div class="form-group">

                <select class="form-control input-sm" name="role">

                    <option disabled<?= is_null($role) ? ' selected' : null ?>>Uprawnienia..</option>

                    <option value="ROLE_USER"<?= 'ROLE_USER' == $role ? ' selected' : null ?>>Użytkownik</option>

                    <option value="ROLE_ADMIN"<?= 'ROLE_ADMIN' == $role ? ' selected' : null ?>>Administrator</option>

                </select>

            </div>

            <div class="form-group">

                <select class="form-control input-sm" name="last_login">

                    <option disabled<?= is_null($last_login) ? ' selected' : null ?>>Ostatnie logowanie</option>

                    <option value="-1 year"<?= '-1 year' == $last_login ? ' selected' : null ?>>Rok temu</option>

                    <option value="-2 years"<?= '-2 years' == $last_login ? ' selected' : null ?>>2 lata temu</option>

                </select>

            </div>

            <button type="submit" class="btn btn-info btn-sm">Submit</button>

        </form>

    </div>

</div>