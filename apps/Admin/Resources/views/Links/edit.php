<?php $view->extend('content') ?>

<?php $view['slots']->set('_sidebar', $view->render('Sidebar/episodes/layout', array('current' => 'links'))) ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="glyphicon glyphicon-ok-sign"></i> Aktualizuj link</h3>
        <div class="pull-right btn-helper">
            <a href="<?= $app->generateUrl('links_index') ?>" class="btn btn-xs btn-primary"><i class="fa fa-reply"></i>Wróć do listy</a>
            <a href="<?= $app->generateUrl('links_delete', array('linkID' => $link->getId())) ?>" class="btn btn-xs btn-danger"><i class="fa fa-times"></i>Usuń</a>
        </div>
    </div>
    <div class="panel-body">
        <form class="form-horizontal" role="form" method="post" action="<?= $app->generateUrl('links_edit', array('linkID' => $link->getId())) ?>">
            <div class="form-group">
                <label for="_category" class="col-sm-2 control-label">Kategoria</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="_category" value="<?= $category['name'] ?>" autocomplete="off" required>
                    <input type="hidden" id="category_id">
                    <span class="help-block">Kategoria musi istnieć i zostać wybrana z listy.</span>
                </div>
            </div>
            <div class="form-group">
                <label for="_episode" class="col-sm-2 control-label">Numer odcinka</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" id="_episode" value="<?= $episode['number'] ?>" required>
                    <input type="hidden" name="link[episode_id]" id="episode_id" value="<?= $link->getEpisodeId() ?>">
                    <span class="help-block">Numer odcinka musi istnieć i zostać wybrany z listy.</span>
                </div>
            </div>
            <div class="form-group">
                <label for="server_id" class="col-sm-2 control-label">Serwer</label>
                <div class="col-sm-10">
                    <select class="form-control" id="server_id" name="link[server_id]" required>
                        <option disabled></option>
                        <?php foreach($servers as $server): ?>
                            <option value="<?= $server['id'] ?>"<?= $link->getServerId() == $server['id'] ? ' selected' : null ?>><?= $server['name'] ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="lang_id" class="col-sm-2 control-label">Język</label>
                <div class="col-sm-10">
                    <select class="form-control" id="lang_id" name="link[lang_id]" required>
                        <?php foreach($languages as $langId => $langName): ?>
                            <option value="<?= $langId ?>"<?= $link->getLangId() == $langId ? ' selected' : null ?>><?= $langName ?></option>
                        <?php endforeach ?>
                    </select>
                    <input type="hidden" name="link[lang]" id="lang" value="<?= $link->getLang() ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="code" class="col-sm-2 control-label">Kod</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="code" required name="link[code]" value="<?= $link->getCode() ?>">
                    <div class="help-block">
                        Wyszukaj w kodzie ciągu który będzie zaznaczony na listingu poniżej:
                        <pre id="server-hint"></pre>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="info" class="col-sm-2 control-label">Dodatkowe informacje</label>
                <div class="col-sm-10">
                    <textarea class="form-control" name="link[info]" id="info" rows="4" maxlength="100"><?= $link->getInfo() ?></textarea>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <input type="hidden" name="link[id]" value="<?= $link->getId() ?>">
                    <button type="submit" class="btn btn-default">Wyślij</button>
                </div>
            </div>
        </form>
    </div>
</div>