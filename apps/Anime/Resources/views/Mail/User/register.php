<?php $view->extend('Mail/layout') ?>

<h2>Witaj <strong><?= $login ?></strong> na stronie AnimeZone.pl!</h2>
Aby móc korzystać w pełni możliwości AnimeZone.pl aktywuj swoje konto klikając w poniższy przycisk:

<p style="line-height:1.4em;color:#444444;font-family:'Helvetica Neue','Helvetica','Arial','sans-serif';margin:0 0 1em 0;margin:20px 0 10px 0;text-align:center;">
    <a href="<?= $app->generateUrl('user_register_confirm', array('code' => $code, 'email' => $email), true) ?>" style="text-decoration:underline;color:#2585b2;border-radius:5em;border:1px solid #11729e;text-decoration:none;color:#fff;background-color:#2585b2;padding:5px 15px;font-size:16px;font-weight:normal;" target="_blank">Aktywuj konto</a>
</p>
