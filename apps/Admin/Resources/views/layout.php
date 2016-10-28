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
    <link href="<?= $view['assets']->getUrl('stylesheet/admin.css') ?>" rel="stylesheet" media="screen">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="<?php echo $view['assets']->getUrl('javascript/html5shiv.js') ?>"></script>
    <script src="<?php echo $view['assets']->getUrl('javascript/respond.min.js') ?>"></script>
    <![endif]-->
    <meta name="description" content="<?php $view['slots']->output('_description', $app->getConfig()->anime->get('description')) ?>">
    <meta name="keywords" content="<?php $view['slots']->output('_keywords', $app->getConfig()->anime->get('keywords')) ?>">
    <title><?php $view['slots']->output('_title', $app->getConfig()->anime->get('title')) ?></title>
    <script>
        var baseUrl = "<?= $app->baseUrl('/') ?>";
        var resourcesUrl = "<?= $view['assets']->getUrl('') ?>";
    </script>
    <?php $view['slots']->output('_head') ?>
</head>
<body>
<div class="masthead">
    <div class="container header">
        <div class="sprites logo"></div>
        <div class="sprites characters desktop"></div>
        <button data-target=".nav-head" data-target-2=".content" type="button" class="navbar-toggle pull-right mobile"><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>
        <div class="desktop log-in logged">
            <p class="text-center title">Panel użytkownika</p>
            <div class="pull-left avatar">
                <a href="<?=$app->basePath('/user/'.$app->getUser()->getUsername()) ?>">
                    <img src="<?= $view['text']->avatar($app->getUser()->getCustomField('avatar'), $view['assets']) ?>" alt="" class="img-rounded">
                </a>
            </div>
            <div class="pull-left links">
                <a href="<?= $app->basePath('/user/'.$app->getUser()->getUsername()) ?>" class="btn btn-sm"><i class="glyphicon glyphicon-user"></i> Mój profil</a><br>
                <a href="<?= $app->basePath('/user/edit') ?>" class="btn btn-sm"><i class="glyphicon glyphicon-edit"></i> Edytuj konto</a><br>
                <?php if($app->getUser()->isAdmin()): ?><a href="<?= $app->basePath() ?>" class="btn btn-sm"><i class="glyphicon glyphicon-home"></i> Główna</a><?php endif ?>
            </div>
            <a href="<?= $app->generateUrl('logout') ?>" class="btn btn-silver btn-sm pull-right"><i class="fa fa-sign-out"></i>Wyloguj się</a>
        </div>
        <a href="<?= $app->basePath('/user/'.$app->getUser()->getUsername()) ?>" class="avatar pull-right mobile">
            <img src="<?= $view['text']->avatar($app->getUser()->getCustomField('avatar'), $view['assets']) ?>" alt="" class="img-rounded">
        </a>
    </div>
</div>
<div class="masthead nav-head">
    <div class="container">
        <ul class="nav mobile">
            <?php if($app->getUser()->isUser()): ?>
                <li><a href="<?= $app->basePath() ?>"><i class="glyphicon glyphicon-home"></i> Główna</a></li>
                <li><a href="<?= $app->generateUrl('logout') ?>"><i class="glyphicon glyphicon-log-out"></i> Wyloguj się</a></li>
            <?php endif ?>
        </ul>
        <ul class="nav">
            <?php foreach($navigation as $i => $item): ?>
                <li><a href="<?= isset($item['target']) ? $item['link'] : $app->baseUrl($item['link']) ?>" target="<?= isset($item['target']) ? $item['target'] : '_top' ?>"><i class="<?= $item['icon'] ?>"></i> <?= $item['name'] ?></a></li>
            <?php endforeach ?>
        </ul>
        <a href="#" class="sticky glyphicon glyphicon-pushpin desktop"></a>
    </div>
</div>
<div class="container content">

    <?php if($app->getSession()->getFlashBag()->has('msg')): ?>
        <?php foreach($app->getSession()->getFlashBag()->get('msg') as $message): ?>

            <div class="alert alert-info fade in">
                <?= $message ?><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            </div>

        <?php endforeach ?>
    <?php endif ?>

    <?php $view['slots']->output('_before_content') ?>
    <?php $view['slots']->output('_content') ?>

</div>
<div class="footer footer-border">
    <div class="container">
        <p>Serwis AnimeZone.pl służy do indeksowania linków powszechnie dostępnych w internecie. Żadne pliki chronione prawem autorskim nie znajdują się na naszym serwerze.</p>
        <p>Wszelkie roszczenia prawne należy kierować pod adresem serwisów publikujących zamieszczone materiały. Więcej informacji w <a href="<?= $app->basePath('/strony/regulamin') ?>">regulaminie</a> oraz <a href="<?= $app->basePath('/strony/polityka-prywatnosci') ?>">polityce prywatności</a>.</p>
        <p>&copy; Copyright 2008-<?= date('Y') ?> <a href="<?= $app->basePath() ?>">www.AnimeZone.pl</a></p>
    </div>
</div>
<a id="back-to-top" href="#" class="btn btn-primary btn-lg back-to-top" role="button"><span class="glyphicon glyphicon-chevron-up"></span></a>
<script src="<?= $view['assets']->getUrl('javascript/jquery-2.1.1.min.js') ?>"></script>
<script src="<?= $view['assets']->getUrl('javascript/device.js') ?>"></script>
<script src="<?= $view['assets']->getUrl('javascript/bootstrap.min.js') ?>"></script>
<script src="<?= $view['assets']->getUrl('javascript/bootstrap-typeahead.min.js') ?>"></script>
<script src="<?= $view['assets']->getUrl('javascript/jquery.autosize.input.min.js') ?>"></script>
<script src="<?= $view['assets']->getUrl('javascript/jquery.autosize.min.js') ?>"></script>
<script src="<?= $view['assets']->getUrl('javascript/core.js') ?>"></script>
<script src="<?= $view['assets']->getUrl('javascript/admin.js') ?>"></script>
<?php $view['slots']->output('_footer') ?>
</body>
</html>