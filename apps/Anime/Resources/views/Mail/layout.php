<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#DDDDDD" style="width:100%;background:#dddddd">
    <tr>
        <td>
            <table border="0" cellspacing="0" cellpadding="0" align="center" width="550" style="width:100%;padding:10px">
                <tr>
                    <td>
                        <div style="direction:ltr;max-width:600px;margin:0 auto">
                            <table border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff" style="width:100%;background-color:#fff;text-align:left;margin:0 auto;max-width:1024px;min-width:320px">
                                <tr>
                                    <td>
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0" height="8" style="width:100%;background-color:#43a4d0;height:8px"><tr><td></td></tr></table>
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="width:100%;background-color:#efefef;padding:0;border-bottom:1px solid #ddd;min-height:60px;">
                                            <tr>
                                                <td>
                                                    <h2 style="padding:0;margin:5px 20px;font-size:20px;line-height:1.4em;font-weight:normal;color:#464646;font-family:&quot;Helvetica Neue&quot;,Helvetica,Arial,sans-serif;">
                                                        <?= $title ?>
                                                    </h2>
                                                </td>
                                                <td style="vertical-align:middle" height="32" width="32" valign="middle" align="right">
                                                    <img border="0" alt="" style="margin:5px 20px 5px 0;vertical-align:middle;vertical-align:middle">
                                                </td>
                                            </tr>
                                        </table>
                                        <table style="width:100%" width="100%" border="0" cellspacing="0" cellpadding="20" bgcolor="#ffffff">
                                            <tr>
                                                <td style="min-height:180px;display:inline-block">
                                                    <?php $view['slots']->output('_content') ?>
                                                </td>
                                            </tr>
                                        </table>
                                        <table border="0" cellspacing="0" width="100%" cellpadding="20" bgcolor="#efefef" style="width:100%;background-color:#efefef;text-align:left;border-top:1px solid #dddddd">
                                            <tr>
                                                <td style="border-top:1px solid #f3f3f3;color:#888;font-family:&quot;Helvetica Neue&quot;,Helvetica,Arial,sans-serif;font-size:14px;background:#efefef;margin:0;padding:10px 20px 20px">
                                                    <p style="direction:ltr;font-size:14px;line-height:1.4em;color:#444444;font-family:&quot;Helvetica Neue&quot;,Helvetica,Arial,sans-serif;margin:0 0 1em 0;font-size:12px;line-height:1.4em;margin:0 0 0 0">
                                                        E-mail został wysłany przez system. Prosimy na niego nie odpowiadać. Jeżeli chcesz się z nami skontaktować, użyj <a href="<?= $app->generateUrl('contact', array(), true) ?>" target="_blank">formularza kontaktowego</a>.
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" height="3" style="width:100%;background-color:#43a4d0;height:3px"><tr><td></td></tr></table>
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>