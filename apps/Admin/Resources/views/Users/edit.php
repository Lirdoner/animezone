<?php $view->extend('content') ?>

<?php $view['slots']->set('_sidebar_addon', $view->render('Sidebar/users/user_search')) ?>
<?php $view['slots']->set('_sidebar', $view->render('Sidebar/users/layout', array('current' => 'users'))) ?>
<?php $view['slots']->start('_footer') ?>
<script>
    $(function(){
        $('#name, #email').on('change keydown keyup', function(){
            var input = $(this);
            var submit = $('#submit');

            $.ajax({
                type: "POST",
                cache: false,
                url: baseUrl + "users/check",
                data: {
                    query: $(this).val()
                },
                dataType: 'json',
                success: function(response){
                    if(!$.isEmptyObject(response))
                    {
                        input.parents('.form-group').addClass('has-error');
                        input.popover({
                            placement: 'bottom',
                            trigger: 'manual',
                            html: true,
                            content: 'Podane dane zostały przypisanny do innego użytkownika: <a href="' +baseUrl+ 'users/' +response.id+ '/edit" target="_blank">' +response.name+ '</a>'
                        }).popover('show');
                        submit.prop('disabled', true);
                    } else
                    {
                        input.popover('destroy').parents('.form-group').removeClass('has-error');
                        submit.prop('disabled', false);
                    }
                },
                error: function(){
                    input.popover({
                        placement: 'bottom',
                        trigger: 'manual',
                        content: 'Wystąpił błąd podczas próby sprawdzenia dostępności loginu/emaila. Odśwież stronę i spróbuj ponownie.'
                    }).popover('show');
                    submit.prop('disabled', true);
                }
            });
        });
    });
</script>
<?php $view['slots']->stop() ?>

<style>
    .popover-title {
        display: none;
    }
</style>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="glyphicon glyphicon-ok-sign"></i> Aktualizuj użytkownika</h3>
        <div class="pull-right btn-helper">
            <a href="<?= $app->generateUrl('users_index') ?>" class="btn btn-xs btn-danger"><i class="fa fa-reply"></i>Wróć do listy</a>
            <a href="<?= $app->basePath('/user/'.$user->getUsername()) ?>" class="btn btn-xs btn-primary">Profil publiczny <i class="fa fa-external-link"></i></a>
        </div>
    </div>
    <div class="panel-body">
        <form class="form-horizontal" role="form" method="post" action="<?= $app->generateUrl('users_edit', array('userID' => $user->getId())) ?>">
            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">Login</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="user[name]" id="name" value="<?= $user->getUsername() ?>" pattern="[\w-\.]{1,32}" required title="Maksymalnie 32 alfanumeryczne znaki.">
                </div>
            </div>
            <div class="form-group">
                <label for="email" class="col-sm-2 control-label">E-mail</label>
                <div class="col-sm-10">
                    <input type="email" class="form-control" name="user[email]" id="email" value="<?= $user->getEmail() ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label for="password" class="col-sm-2 control-label">Hasło</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="user[password]" id="password" value autocomplete="off">
                    <span class="help-block">Po zmianie hasła wszystkie aktywne sesje dla tego konta zostaną usuniete.</span>
                </div>
            </div>
            <div class="form-group">
                <label for="ip" class="col-sm-2 control-label">Adres IP</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="user[ip]" id="ip" value="<?= $user->getIp() ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label for="last_login" class="col-sm-2 control-label">Ostatnie logowanie</label>
                <div class="col-sm-10">
                    <input type="datetime" class="form-control" name="user[last_login]" id="last_login" value="<?= $user->getLastLogin()->format('Y-m-d H:i:s') ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label for="date_created" class="col-sm-2 control-label">Data rejestracji</label>
                <div class="col-sm-10">
                    <input type="datetime" class="form-control" name="user[date_created]" id="date_created" value="<?= $user->getDateCreated()->format('Y-m-d H:i:s') ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label for="enabled" class="col-sm-2 control-label">Status</label>
                <div class="col-sm-10">
                    <select class="form-control" id="enabled" name="user[enabled]">
                        <option value="0"<?= !$user->getEnabled() ? ' selected' : null ?>>Nieaktywny</option>
                        <option value="1"<?= $user->getEnabled() == 1 ? ' selected' : null ?>>Aktywny</option>
                        <option value="2"<?= $user->getEnabled() == 2 ? ' selected' : null ?>>Zablokowany</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="role" class="col-sm-2 control-label">Uprawnienia</label>
                <div class="col-sm-10">
                    <select class="form-control" id="role" name="user[role]">
                        <option value="ROLE_USER"<?= $user->isUser() ? ' selected' : null ?>>Użytkownik</option>
                        <option value="ROLE_ADMIN"<?= $user->isAdmin() ? ' selected' : null ?>>Administrator</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="location" class="col-sm-2 control-label">Miejscowość</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="user[location]" id="location" value="<?= $user->getCustomField('location') ?>" pattern=".{1,30}" required title="Maksymalnie 30 znaków.">
                </div>
            </div>
            <div class="form-group">
                <label for="birthdate" class="col-sm-2 control-label">Data urodzenia</label>
                <div class="col-sm-10">
                    <input type="date" class="form-control" name="user[birthdate]" id="birthdate" value="<?= $user->getCustomField('birthdate') ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label for="gender" class="col-sm-2 control-label">Płeć</label>
                <div class="col-sm-10">
                    <select class="form-control" id="role" name="user[gender]">
                        <option value="1"<?= $user->getCustomField('gender') == 1 ? ' selected' : null ?>>Mężczyzna</option>
                        <option value="2"<?= $user->getCustomField('gender') == 2 ? ' selected' : null ?>>Kobieta</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="facebook_id" class="col-sm-2 control-label">Facebook</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" name="user[facebook_id]" value="<?= $user->getCustomField('facebook_id', 0) ?>" id="facebook_id">
                    <span class="help-block">Poprawny ID można sprawdzić dopisując do <a href="https://graph.facebook.com/MateuszKapuscinski" target="_blank">adresu</a> <kbd>username</kbd> przykładowo <kbd>MateuszKapuscinski</kbd></span>
                </div>
            </div>
            <div class="form-group">
                <label for="favorites" class="col-sm-2 control-label">Ulubione</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" name="user[favorites]" value="<?= $user->getCustomField('favorites', 0) ?>" id="favorites" required>
                </div>
            </div>
            <div class="form-group">
                <label for="rated" class="col-sm-2 control-label">Ocenione</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" name="user[rated]" value="<?= $user->getCustomField('rated', 0) ?>" id="rated" required>
                </div>
            </div>
            <div class="form-group">
                <label for="commented" class="col-sm-2 control-label">Komentarzy</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" name="user[commented]" value="<?= $user->getCustomField('commented', 0) ?>" id="commented" required>
                </div>
            </div>
            <div class="form-group">
                <label for="watching" class="col-sm-2 control-label">Oglądanych</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" name="user[watching]" value="<?= $user->getCustomField('watching', 0) ?>" id="watching" required>
                </div>
            </div>
            <div class="form-group">
                <label for="watched" class="col-sm-2 control-label">Obejrzanych</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" name="user[watched]" value="<?= $user->getCustomField('watched', 0) ?>" id="watched" required>
                </div>
            </div>
            <div class="form-group">
                <label for="plans" class="col-sm-2 control-label">Planowanych</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" name="user[plans]" value="<?= $user->getCustomField('plans', 0) ?>" id="plans" required>
                </div>
            </div>
            <div class="form-group">
                <label for="stopped" class="col-sm-2 control-label">Wstrzymanych</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" name="user[stopped]" value="<?= $user->getCustomField('stopped', 0) ?>" id="stopped" required>
                </div>
            </div>
            <div class="form-group">
                <label for="abandoned" class="col-sm-2 control-label">Porzuconych</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" name="user[abandoned]" value="<?= $user->getCustomField('abandoned', 0) ?>" id="abandoned" required>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <input type="hidden" name="user[id]" value="<?= $user->getId() ?>">
                    <button type="submit" class="btn btn-default" id="submit">Wyślij</button>
                </div>
            </div>
        </form>
    </div>
</div>