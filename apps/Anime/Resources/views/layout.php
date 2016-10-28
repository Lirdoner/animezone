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
    <script src="<?= $view['assets']->getUrl('javascript/html5shiv.js') ?>"></script>
    <script src="<?= $view['assets']->getUrl('javascript/respond.min.js') ?>"></script>
    <![endif]-->
    <meta name="description" content="<?php $view['slots']->output('_description', $app->getConfig()->anime->get('description')) ?>">
    <meta name="keywords" content="<?php $view['slots']->output('_keywords', $app->getConfig()->anime->get('keywords')) ?>">
    <title><?php $view['slots']->output('_title', $app->getConfig()->anime->get('title')) ?></title>
    <script>
        var baseUrl = "<?= $app->baseUrl('/') ?>";
        var resourcesUrl = "<?= $view['assets']->getUrl('') ?>";
        var __gaq = "<?php $view['slots']->output('video_prefix') ?>";
		
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		ga('create', 'UA-5582841-2', 'auto');
		ga('send', 'pageview');
    </script>
    <?php $view['slots']->output('_head') ?>
</head>
<body>
<div class="masthead">
    <div class="container header">
        <div class="sprites logo"></div>
        <div class="sprites characters desktop"></div>
        <button data-target=".nav-head" data-target-2=".content" type="button" class="navbar-toggle pull-right mobile"><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>
        <?php if(!$app->getUser()->isUser()): ?>
            <div class="desktop log-in">
                <div class="login-form">
                    <form action="<?= $app->generateUrl('login') ?>" method="post">
                        <div class="input-group input-group-sm">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input type="text" class="form-control" name="login" placeholder="Użytkownik lub E-mail">
                        </div>
                        <div class="input-group input-group-sm">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                            <input type="password" class="form-control" name="password" placeholder="Hasło">
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" name="rememberMe"> Zapamiętaj mnie</label>
                        </div>
                        <div class="buttons text-center">
                            <button type="submit" class="btn btn-group-vertical btn-silver btn-sm"><i class="fa fa-sign-in"></i> Zaloguj się</button>
                            <a href="<?= $app->generateUrl('login_facebook') ?>" class="btn btn-group-vertical btn-primary btn-sm"><i class="fa fa-facebook-square"></i> Zaloguj przez FB</a>
                        </div>
                    </form>
                </div>
                <div class="restore text-center">
                    <div><a href="<?= $app->generateUrl('user_restore') ?>">Zapomniałem hasła</a></div>
                    <div><a href="<?= $app->generateUrl('user_register') ?>">Zarejestruj się</a></div>
                </div>
            </div>
        <?php else: ?>
            <div class="desktop log-in logged">
                <p class="text-center title">Panel użytkownika</p>
                <div class="pull-left avatar">
                    <a href="<?= $app->generateUrl('user_profile', array('user_name' => $app->getUser()->getUsername())) ?>">
                        <img src="<?= $view['text']->avatar($app->getUser()->getCustomField('avatar'), $view['assets']) ?>" alt="" class="img-rounded">
                    </a>
                </div>
                <div class="pull-left links">
                    <a href="<?= $app->generateUrl('user_profile', array('user_name' => $app->getUser()->getUsername())) ?>" class="btn btn-sm"><i class="glyphicon glyphicon-user"></i> Mój profil</a><br>
                    <a href="<?= $app->generateUrl('user_edit_profile') ?>" class="btn btn-sm"><i class="glyphicon glyphicon-edit"></i> Edytuj konto</a><br>
                    <?php if($app->getUser()->isAdmin()): ?><a href="<?php echo $app->generateUrl('admin') ?>/" class="btn btn-sm"><i class="glyphicon glyphicon-cog"></i> Administracja</a><?php endif ?>
                </div>
                <a href="<?= $app->generateUrl('logout') ?>" class="btn btn-silver btn-sm pull-right"><i class="fa fa-sign-out"></i>Wyloguj się</a>
            </div>
            <a href="<?= $app->generateUrl('user_profile', array('user_name' => $app->getUser()->getUsername())) ?>" class="avatar pull-right mobile">
                <img src="<?= $view['text']->avatar($app->getUser()->getCustomField('avatar'), $view['assets']) ?>" alt="" class="img-rounded">
            </a>
        <?php endif ?>
    </div>
</div>
<div class="masthead nav-head">
    <div class="container">
        <ul class="nav mobile">
            <?php if($app->getUser()->isUser()): ?>
                <li><a href="<?= $app->generateUrl('user_profile', array('user_name' => $app->getUser()->getUsername())) ?>"><i class="glyphicon glyphicon-user"></i> Mój profil</a></li>
                <li><a href="<?= $app->generateUrl('user_edit_profile') ?>"><i class="glyphicon glyphicon-edit"></i> Edytuj konto</a></li>
                <?php if($app->getUser()->isAdmin()): ?><li></li><?php endif ?>
                <li><a href="<?= $app->generateUrl('logout') ?>"><i class="glyphicon glyphicon-log-out"></i> Wyloguj się</a></li>
            <?php else: ?>
                <li><a href="<?= $app->generateUrl('login_mobile') ?>"><i class="fa fa-sign-in"></i> Zaloguj się</a></li>
                <li><a href="<?= $app->generateUrl('user_register') ?>"><i class="fa fa-users"></i> Zarejestruj się</a></li>
                <li><a href="<?= $app->generateUrl('user_restore') ?>"><i class="fa fa-life-ring"></i> Reset hasła</a></li>
            <?php endif ?>
        </ul>
        <ul class="nav">
            <?php foreach($navigation as $i => $item): ?>
                <?php if(empty($item['submenu'])): ?>
                    <li><a href="<?= strstr($item['link'], 'http') ? $item['link'] : $app->baseUrl($item['link']) ?>"><i class="<?= $item['icon'] ?>"></i> <?= $item['name'] ?></a></li>
                <?php else: ?>
                    <li>
                        <a href="#" data-toggle="dropdown"><i class="<?= $item['icon'] ?>"></i> <?= $item['name'] ?> <span class="caret"></span></a>
                        <ul class="nav dropdown-menu">
                            <?php foreach($item['submenu'] as $i => $subitem): ?>
                                <li><a href="<?= strstr($subitem['link'], 'http') ? $subitem['link'] : $app->baseUrl($subitem['link']) ?>"><i class="<?= $subitem['icon'] ?>"></i> <?= $subitem['name'] ?></a></li>
                            <?php endforeach ?>
                        </ul>
                    </li>
                <?php endif ?>
            <?php endforeach ?>
        </ul>
        <a href="#" class="sticky glyphicon glyphicon-pushpin desktop"></a>
    </div>
</div>
<div class="container content">

    <?php if($app->getSession()->getFlashBag()->has('msg')): ?>
        <?php foreach($app->getSession()->getFlashBag()->get('msg') as $type => $message): ?>

            <div class="alert alert-<?= is_string($type) ? $type : 'info' ?> fade in">
                <?= $message ?><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            </div>

        <?php endforeach ?>
    <?php elseif(!$app->getUser()->isAdmin()): ?>
        <div class="container rek gora desktop"><?= $ads->random('gora') ?></div>
    <?php endif ?>

    <?php $view['slots']->output('_before_content') ?>
    <?php $view['slots']->output('_content') ?>

</div>
<div class="footer footer-border">
    <div class="container">
        <p>Serwis AnimeZone.pl służy do indeksowania linków powszechnie dostępnych w internecie. Żadne pliki chronione prawem autorskim nie znajdują się na naszym serwerze.</p>
        <p>Wszelkie roszczenia prawne należy kierować pod adresem serwisów publikujących zamieszczone materiały. Więcej informacji w <a href="<?= $app->generateUrl('pages', array('alias' => 'regulamin')) ?>">regulaminie</a> oraz <a href="<?= $app->generateUrl('pages', array('alias' => 'polityka-prywatnosci')) ?>">polityce prywatności</a>.</p>
        <p>&copy; Copyright 2008-<?= date('Y') ?> <a href="<?= $app->generateUrl('homepage', array(), true) ?>">www.AnimeZone.pl</a></p>
		<script id="_wauxy7">var _wau = _wau || []; _wau.push(["small", "hoxhl6ndfsvs", "xy7"]);
		(function() {var s=document.createElement("script"); s.async=true;
		s.src="http://widgets.amung.us/small.js";
		document.getElementsByTagName("head")[0].appendChild(s);
		})();</script>
    </div>
</div>
<div class="myTestAd myAdBanner"></div>
<a id="back-to-top" href="#" class="btn btn-primary btn-lg back-to-top" role="button"><span class="glyphicon glyphicon-chevron-up"></span></a>
<script src="<?= $view['assets']->getUrl('javascript/jquery-2.1.1.min.js') ?>"></script>
<script src="<?= $view['assets']->getUrl('javascript/device.js') ?>"></script>
<script src="<?= $view['assets']->getUrl('javascript/bootstrap.min.js') ?>"></script>
<script src="<?= $view['assets']->getUrl('javascript/core.js') ?>"></script>
<?php $view['slots']->output('_footer') ?>

<?php if(true): ?>
    <!-- <span class="hidden">wygenerowane w <?= number_format(microtime(true) - $app->getConfig()->framework->get('start_time'), 4) ?>s, z użyciem <?= round(memory_get_usage()/1024,2) ?>kb pamięci.</span> -->
<?php else: ?>
    <div class="rek bok desktop"><?= $ads->random('bok') ?></div>
    <script type="text/javascript">
        var _pop = _pop || [];
        _pop.push(['siteId', 285671]);
        _pop.push(['minBid', 0]);
        _pop.push(['popundersPerIP', <?= $app->getUser()->isUser() ? 2 : 0 ?>]);
        _pop.push(['delayBetween', 3600]);
        _pop.push(['default', false]);
        _pop.push(['defaultPerDay', 0]);
        _pop.push(['topmostLayer', false]);

        (function() {
            if(!device.mobile()) {
                var pa = document.createElement('script');
                pa.type = 'text/javascript';
                pa.async = true;
                var s = document.getElementsByTagName('script')[0];
                pa.src = '//c1.popads.net/pop.js';
                pa.onerror = function () {
                    var sa = document.createElement('script');
                    sa.type = 'text/javascript';
                    sa.async = true;
                    sa.src = '//c2.popads.net/pop.js';
                    s.parentNode.insertBefore(sa, s);
                };
                s.parentNode.insertBefore(pa, s);
            }
        })();
	</script>
	<script data-cfasync="false" type="text/javascript" src="http://www.tradeadexchange.com/a/display.php?r=986933"></script> 
<?php endif ?>
</body>
</html>