<?php $view->extend('content') ?>

<?php $view['slots']->set('_title', 'Rejestracja - '.$app->getConfig()->anime->get('title')) ?>
<?php $view['slots']->set('_sidebar', $view->render('Sidebar/faq', array('sidebar' => $sidebar))) ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="glyphicon glyphicon-user"></i> Rejestracja</h3>
    </div>
    <div class="panel-body">
        <?php if(!empty($error)): ?>
            <div class="bs-callout bs-callout-danger">
                <h4>Jedno lub więcej pól formularza jest niepoprawne</h4>
                <p>
                    <?php foreach($error as $msg): ?>
                        - <?= $msg ?><br>
                    <?php endforeach ?>
                </p>
            </div>
        <?php else: ?>
            <div class="bs-callout bs-callout-info">
                <p>Wszystkie pola są wymagane.</p>
            </div>
        <?php endif ?>
        <form class="form-horizontal" role="form" method="post" action="<?= $app->generateUrl('user_register') ?>">
            <div class="form-group">
                <label for="inputLogin" class="col-sm-2 control-label">Login</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="user[name]" id="inputLogin" value="<?= $user->getUsername() ?>" placeholder="Login/nick" pattern="[\w-\.]{3,32}" required title="Minimum 3 znaki, maksimum 32 znaków. Tylko znaki alfanumeryczne.">
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail" class="col-sm-2 control-label">E-mail</label>
                <div class="col-sm-10">
                    <input type="email" required size="40" class="form-control" name="user[email]" id="inputEmail" value="<?= $user->getEmail() ?>" placeholder="E-mail">
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword" class="col-sm-2 control-label">Hasło</label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" name="user[password]" id="inputPassword" placeholder="Hasło" pattern=".{5,32}" required title="Minimum 5 znaki, maksimum 32 znaków.">
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword2" class="col-sm-2 control-label">Powtórz hasło</label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" name="user[password2]" id="inputPassword2" placeholder="Powtórz hasło" pattern=".{5,32}" required title="Minimum 5 znaki, maksimum 32 znaków.">
                </div>
            </div>
            <div class="form-group">
                <label for="inputLocation" class="col-sm-2 control-label">Miejscowość</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="user[location]" id="inputLocation" value="<?= $user->getCustomField('location') ?>" placeholder="Miejscowość" pattern=".{3,30}" required title="Minimum 3 znaki, maksimum 32 znaków.">
                </div>
            </div>
            <div class="form-group">
                <label for="inputBirthdate" class="col-sm-2 control-label">Data urodzenia</label>
                <div class="col-sm-10">
                    <input type="date" class="form-control" name="user[birthdate]" id="inputBirthdate" value="<?= $user->getCustomField('birthdate') ?>" placeholder="data w formacie 2001-12-31" pattern="\d{4}-\d{1,2}-\d{1,2}" required>
                    <span class="help-block">Data w formacie: rok-miesiąc-dzień (2001-12-31)</span>
                </div>
            </div>
            <div class="form-group">
                <label for="inputGender" class="col-sm-2 control-label">Płeć</label>
                <div class="col-sm-10">
                    <div class="radio">
                        <label><input type="radio" name="user[gender]" value="1"<?= $user->getCustomField('gender') == 1 ? ' checked' : null ?>> Mężczyzna</label>
                    </div>
                    <div class="radio">
                        <label><input type="radio" name="user[gender]" value="2"<?= $user->getCustomField('gender') == 2 ? ' checked' : null ?>> Kobieta</label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputCode" class="col-sm-2 control-label">Kod z obrazka</label>
                <div class="col-sm-10">
                    <div class="thumbnail image-captcha">
                        <img src="<?= $app->generateUrl('_captcha') ?>" class="img-responsive">
                    </div>
                    <div class="input-group input-captcha">
                        <input type="text" class="form-control" name="code" id="inputCode" placeholder="kod" required>
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button"><i class="glyphicon glyphicon-refresh"></i> </button>
                        </span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-default">Wyślij</button>
                </div>
            </div>
        </form>
    </div>
</div>