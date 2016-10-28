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
        <h3 class="panel-title"><i class="glyphicon glyphicon-plus-sign"></i> Nowy użytkownik</h3>
        <a href="<?= $app->generateUrl('users_index') ?>" class="btn btn-xs btn-danger pull-right btn-helper"><i class="fa fa-reply"></i>Wróć do listy</a>
    </div>
    <div class="panel-body">
        <form class="form-horizontal" role="form" method="post" action="<?= $app->generateUrl('users_create') ?>">
            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">Login</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="user[name]" id="name" placeholder="Nick" pattern="[\w-\.]{1,32}" required title="Maksymalnie 32 alfanumeryczne znaki.">
                </div>
            </div>
            <div class="form-group">
                <label for="email" class="col-sm-2 control-label">E-mail</label>
                <div class="col-sm-10">
                    <input type="email" class="form-control" name="user[email]" id="email" placeholder="Adres e-mail" required>
                </div>
            </div>
            <div class="form-group">
                <label for="enabled" class="col-sm-2 control-label">Status</label>
                <div class="col-sm-10">
                    <select class="form-control" id="enabled" name="user[enabled]">
                        <option value="0">Nieaktywny</option>
                        <option value="1" selected>Aktywny</option>
                        <option value="2">Zablokowany</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="role" class="col-sm-2 control-label">Uprawnienia</label>
                <div class="col-sm-10">
                    <select class="form-control" id="role" name="user[role]">
                        <option value="ROLE_USER" selected>Użytkownik</option>
                        <option value="ROLE_ADMIN">Administrator</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="location" class="col-sm-2 control-label">Miejscowość</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="user[location]" id="location" placeholder="Lokalizacja" pattern=".{1,30}" required title="Maksymalnie 30 znaków.">
                </div>
            </div>
            <div class="form-group">
                <label for="birthdate" class="col-sm-2 control-label">Data urodzenia</label>
                <div class="col-sm-10">
                    <input type="date" class="form-control" name="user[birthdate]" id="birthdate" required>
                </div>
            </div>
            <div class="form-group">
                <label for="gender" class="col-sm-2 control-label">Płeć</label>
                <div class="col-sm-10">
                    <select class="form-control" id="role" name="user[gender]">
                        <option value="1">Mężczyzna</option>
                        <option value="2">Kobieta</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="facebook_id" class="col-sm-2 control-label">Facebook</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" name="user[facebook_id]" value="0" id="facebook_id" placeholder="FacebookID">
                    <span class="help-block">Poprawny ID można sprawdzić dopisując do <a href="https://graph.facebook.com/MateuszKapuscinski" target="_blank">adresu</a> <kbd>username</kbd> przykładowo <kbd>MateuszKapuscinski</kbd></span>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-default" id="submit">Wyślij</button>
                </div>
            </div>
        </form>
    </div>
</div>