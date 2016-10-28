<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="<?php echo $view['assets']->getUrl('images/favicon.ico') ?>" type="image/x-icon">
    <link href="<?= $view['assets']->getUrl('stylesheet/bootstrap.min.css') ?>" rel="stylesheet" media="screen">
    <link href="<?= $view['assets']->getUrl('stylesheet/bootstrap-theme.min.css') ?>" rel="stylesheet" media="screen">
    <link href="<?= $view['assets']->getUrl('stylesheet/font-awesome.min.css') ?>" rel="stylesheet" media="screen">
    <link href="<?= $view['assets']->getUrl('stylesheet/style.css') ?>" rel="stylesheet" media="screen">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="<?php echo $view['assets']->getUrl('javascript/html5shiv.js') ?>"></script>
    <script src="<?php echo $view['assets']->getUrl('javascript/respond.min.js') ?>"></script>
    <![endif]-->
    <title><?php $view['slots']->output('_title', $app->getConfig()->anime->get('title')) ?></title>
</head>
<body>
<div class="container" style="margin-top: 10%">
    <div class="row col-sm-7 col-sm-offset-3">
        <div class="well well-lg">
            <legend>Dalszy dostęp wymaga uwierzytelnienia</legend>
            <?php if($msg): ?><div class="alert alert-danger fade in"><?= $msg ?></div><?php endif ?>
            <form method="post" action="<?= $app->generateUrl('login') ?>">
                <div class="form-group">
                    <div class="input-group input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                        <input type="text" class="form-control" name="login" placeholder="Użytkownik lub E-mail">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                        <input type="password" class="form-control" name="password" placeholder="Hasło">
                    </div>
                </div>
                <button type="submit" name="submit" class="btn btn-info btn-block">Zaloguj się</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>