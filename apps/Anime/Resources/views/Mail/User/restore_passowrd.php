<?php $view->extend('Mail/layout') ?>

Ktoś poprosił o zmianę hasła dla konta: <strong><?php echo $login ?></strong>, na stronie AnimeZone.pl <br>

Jeżeli chcesz zmienić hasło, kliknij poniższy przycisk:

<p style="line-height:1.4em;color:#444444;font-family:'Helvetica Neue','Helvetica','Arial','sans-serif';margin:0 0 1em 0;margin:20px 0 10px 0;text-align:center;">
    <a href="<?= $app->generateUrl('user_restore_confirm', array('code' => $code), true) ?>" style="text-decoration:underline;color:#2585b2;border-radius:5em;border:1px solid #11729e;text-decoration:none;color:#fff;background-color:#2585b2;padding:5px 15px;font-size:16px;font-weight:normal;" target="_blank">Reset hasła</a>
</p>

Jeżeli to nie Ty poprosiłeś o zmianę hasła, zignoruj tą wiadomość.<br><br>
Kod aktywacyjny działa przez 24godziny. Po upływie tego czasu należy wygenerować nowy kod pod adresem zamieszczonym <a href="<?= $app->generateUrl('user_restore', array(), true) ?>">tutaj &raquo;</a>
