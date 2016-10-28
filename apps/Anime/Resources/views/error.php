<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?= $view['assets']->getUrl('stylesheet/bootstrap.min.css') ?>" rel="stylesheet" media="screen">
    <link href="<?= $view['assets']->getUrl('stylesheet/bootstrap-theme.min.css') ?>" rel="stylesheet" media="screen">
    <link href="<?= $view['assets']->getUrl('stylesheet/error.css') ?>" rel="stylesheet" media="screen">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="<?= $view['assets']->getUrl('javascript/html5shiv.js') ?>"></script>
    <script src="<?= $view['assets']->getUrl('javascript/respond.min.js') ?>"></script>
    <![endif]-->
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="error-template">
                <h1>Oops!</h1>
                <h2><?= $status_code.' '.$status_text ?></h2>
                <div class="error-details">
                    <?= $app->getConfig()->anime->error->get($status_code, 'Sorry, an error has occured, Requested '.$status_text) ?>
                </div>
                <div class="error-actions">
                    <a href="<?= $app->generateUrl('homepage') ?>" class="btn btn-primary btn-lg"><i class="glyphicon glyphicon-home"></i> Wróć na stronę główną </a>
                    <a href="<?= $app->generateUrl('contact') ?>" class="btn btn-default btn-lg"><i class="glyphicon glyphicon-envelope"></i> Skontaktuj się z nami </a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>