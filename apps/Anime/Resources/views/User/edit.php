<?php $view->extend('content') ?>

<?php $view['slots']->set('_title', 'Edycja profilu - '.$app->getConfig()->anime->get('title')) ?>
<?php $view['slots']->set('_sidebar', $view->render('Sidebar/faq', array('sidebar' => $sidebar))) ?>

<?php $view['slots']->start('_footer') ?>
    <script src="<?= $view['assets']->getUrl('javascript/user.edit.js') ?>"></script>
<?php $view['slots']->stop() ?>

<div class="panel panel-default user-edit">
    <div class="panel-heading">
        <h3 class="panel-title">
            <i class="glyphicon glyphicon-user"></i> <span class="title">Edycja profilu</span>
        </h3>
    </div>
    <div class="panel-body">
        <ul class="nav nav-tabs nav-justified">
            <li class="active"><a href="#avatar" data-toggle="tab">Avatar</a></li>
            <li><a href="#info" data-toggle="tab">Informacje</a></li>
            <?php if(!$user->getCustomField('facebook_id')): ?><li><a href="#password" data-toggle="tab">Hasło</a></li><?php endif ?>
        </ul>
        <?php if(!empty($error)): ?>
            <div class="bs-callout bs-callout-danger">
                <h4>Jedno lub więcej pól formularza jest niepoprawne</h4>
                <p>
                    <?php foreach($error as $msg): ?>
                        - <?= $msg ?><br>
                    <?php endforeach ?>
                </p>
            </div>
        <?php endif ?>
        <div class="tab-content">
            <div class="tab-pane fade in active" id="avatar">
                <div class="bs-callout bs-callout-info">
                    <p>Po najechaniu kursorem na miniaturę avatara, zostanie ona powiększona.</p>
                </div>
                <form class="form-horizontal" role="form" method="post" action="<?= $app->generateUrl('user_edit_profile') ?>#avatar" enctype="multipart/form-data">
                    <div class="form-group accordion">
                        <label for="source-computer" class="col-sm-2 control-label">Z dysku</label>
                        <div class="col-sm-10">
                            <div class="input-group image-preview">
                                <span class="input-group-addon">
                                    <input type="radio" name="source" value="computer" class="radio-inline" id="source-computer">
                                </span>
                                <input type="text" class="form-control image-preview-filename" disabled="disabled">
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default image-preview-clear" style="display:none;"><span class="glyphicon glyphicon-remove"></span> Usuń</button>
                                    <span class="btn btn-default image-preview-input">
                                        <span class="glyphicon glyphicon-folder-open"></span>
                                        <span class="image-preview-input-title">Przeglądaj</span>
                                        <input type="file" accept="image/png, image/jpeg, image/gif" name="picture_file"  id="source-computer">
                                    </span>
                                </span>
                            </div>
                            <span class="help-block collapse">Max. rozmiar pliku: do 100Kb / Max. rozmiar grafiki: do 100x100 pikseli</span>
                        </div>
                    </div>
                    <div class="form-group internet">
                        <div class="accordion">
                            <label for="source-gravatar" class="col-sm-2 col-xs-12 control-label">Z internetu</label>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                <div class="input-group preview">
                                <span class="input-group-addon">
                                    <input type="radio" name="source" value="gravatar" class="radio-inline" id="source-gravatar"<?= strstr($user->getCustomField('avatar'), 'gravatar') ? ' checked="true"' : null ?>>
                                </span>
                                <span class="input-group-btn">
                                    <img src="http://www.gravatar.com/avatar/<?= md5($user->getEmail()) ?>.jpg?s=100" class="avatar">
                                </span>
                                    <span class="input-group-addon">Gravatar</span>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-sm-offset-2 col-xs-offset-0 col-sm-10 collapse">
                                <span class="help-block">Użyj swojej grafiki z <a href="http://www.gravatar.com/" target="_blank">Gravatar.com</a>.</span>
                            </div>
                        </div>
                        <?php if(!empty($facebook)): ?>
                            <div class="accordion">
                                <div class="col-sm-12 col-xs-12"></div>
                                <div class="col-sm-offset-2 col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                    <div class="input-group preview">
                                    <span class="input-group-addon">
                                        <input type="radio" name="source" value="facebook" class="radio-inline" id="source-facebook"<?= strstr($user->getCustomField('avatar'), 'fbcdn') ? ' checked="true"' : null ?>>
                                    </span>
                                    <span class="input-group-btn">
                                        <img src="<?= $facebook ?>" class="avatar">
                                    </span>
                                    <span class="input-group-addon">
                                        Facebook
                                    </span>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-sm-offset-2 col-xs-offset-0 col-sm-10 collapse">
                                    <span class="help-block">Użyj swojego zdjęcia z Facebooka.</span>
                                </div>
                            </div>
                        <?php endif ?>
                    </div>
                    <div class="form-group accordion">
                        <label for="default" class="col-sm-2 col-xs-12 control-label">Domyślny</label>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <input type="radio" name="source" value="default" class="radio-inline" id="default"<?= !$user->getCustomField('avatar') ? ' checked="true"' : null ?>>
                                </span>
                                <span class="input-group-btn">
                                    <img src="<?= $view['assets']->getUrl('avatars/brak.png') ?>" class="avatar default">
                                </span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-offset-2 col-xs-offset-0 col-sm-10 collapse">
                            <span class="help-block">Użyj domyślnego grafiki z tej strony</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-default">Wyślij</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="tab-pane fade" id="info">
                <form class="form-horizontal" role="form" method="post" action="<?= $app->generateUrl('user_edit_profile') ?>#info">
                    <?php if($user->getCustomField('facebook_id')): ?>
                        <div class="form-group">
                            <label for="inputLogin" class="col-sm-2 control-label">Login</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="name" id="inputLogin" value="<?= $user->getUsername() ?>" placeholder="Login/nick" pattern="[\w-\.]{3,32}" required title="Minimum 3 znaki, maksimum 32 znaków. Tylko znaki alfanumeryczne.">
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="form-group">
                            <label for="inputEmail" class="col-sm-2 control-label">E-mail</label>
                            <div class="col-sm-10">
                                <input type="email" required size="40" class="form-control" name="email" id="inputEmail" value="<?= $user->getEmail() ?>" placeholder="E-mail">
                            </div>
                        </div>
                    <?php endif ?>
                    <div class="form-group">
                        <label for="inputLocation" class="col-sm-2 control-label">Miejscowość</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="location" id="inputLocation" value="<?= $user->getCustomField('location') ?>" placeholder="Miejscowość" pattern=".{3,30}" required title="Minimum 3 znaki, maksimum 32 znaków.">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputBirthdate" class="col-sm-2 control-label">Data urodzenia</label>
                        <div class="col-sm-10">
                            <input type="date" class="form-control" name="birthdate" id="inputBirthdate" value="<?= $user->getCustomField('birthdate') ?>" placeholder="data w formacie 2001-12-31" pattern="\d{4}-\d{1,2}-\d{1,2}" required>
                            <span class="help-block">Data w formacie: rok-miesiąc-dzień (2001-12-31)</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-default">Wyślij</button>
                        </div>
                    </div>
                </form>
            </div>
            <?php if(!$user->getCustomField('facebook_id')): ?>
                <div class="tab-pane fade" id="password">
                    <form class="form-horizontal" role="form" method="post" action="<?= $app->generateUrl('user_edit_profile') ?>#password">
                        <div class="form-group">
                            <label for="inputPasswordCurrent" class="col-sm-2 control-label">Aktualne hasło</label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" name="password[current]" id="inputPasswordCurrent" placeholder="Aktualne hasło" pattern=".{5,32}" required title="Minimum 5 znaki, maksimum 32 znaków.">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputPasswordNew" class="col-sm-2 control-label">Nowe hasło</label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" name="password[new]" id="inputPasswordNew" placeholder="Nowe hasło" pattern=".{5,32}" required title="Minimum 5 znaki, maksimum 32 znaków.">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputPasswordNew2" class="col-sm-2 control-label">Powtórz nowe hasło</label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" name="password[new2]" id="inputPasswordNew2" placeholder="Powtórz nowe hasło" pattern=".{5,32}" required title="Minimum 5 znaki, maksimum 32 znaków.">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-default">Wyślij</button>
                            </div>
                        </div>
                    </form>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>