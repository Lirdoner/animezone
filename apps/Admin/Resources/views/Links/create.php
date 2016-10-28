<?php $view->extend('content') ?>

<?php $view['slots']->set('_sidebar', $view->render('Sidebar/episodes/layout', array('current' => 'links'))) ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="glyphicon glyphicon-plus-sign"></i> Nowy link</h3>
        <a href="<?= $app->generateUrl('links_index') ?>" class="btn btn-xs btn-danger pull-right btn-helper"><i class="fa fa-reply"></i>Wróć do listy</a>
    </div>
    <div class="panel-body">
        <form class="form-horizontal" role="form" method="post" id="createEpisode" action="<?= $app->generateUrl('links_create') ?>">
            <div class="form-group">
                <label for="_category" class="col-sm-2 control-label">Kategoria</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="_category" name="categoryName" autocomplete="off" required value="<?= $categoryName ?>">
                    <input type="hidden" id="category_id" name="categoryId" value="<?= $categoryId ?>">
                    <span class="help-block">Kategoria musi istnieć i zostać wybrana z listy.</span>
                </div>
            </div>
            <div class="form-group">
                <label for="_episode" class="col-sm-2 control-label">Numer odcinka</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" id="_episode" required name="episodeNumber" value="<?= $episodeNumber ?>">
                    <input type="hidden" name="link[episode_id]" id="episode_id" value="<?= $episodeId ?>">
                    <span class="help-block">Numer odcinka musi istnieć i zostać wybrany z listy.</span>
                </div>
            </div>
            <div class="form-group">
                <label for="server_id" class="col-sm-2 control-label">Serwer</label>
                <div class="col-sm-10">
                    <select class="form-control" id="server_id" name="link[server_id]" required>
                        <option disabled></option>
                        <?php foreach($servers as $server): ?>
                            <option value="<?= $server['id'] ?>"><?= $server['name'] ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="lang_id" class="col-sm-2 control-label">Język</label>
                <div class="col-sm-10">
                    <select class="form-control" id="lang_id" name="link[lang_id]" required>
                        <?php foreach($languages as $langId => $langName): ?>
                            <option value="<?= $langId ?>"<?= 2 == $langId ? ' selected': null ?>><?= $langName ?></option>
                        <?php endforeach ?>
                    </select>
                    <input type="hidden" value="PL" name="link[lang]" id="lang">
                </div>
            </div>
            <div class="form-group">
                <label for="code" class="col-sm-2 control-label">Kod</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="code" required name="link[code]">
                    <div class="help-block">
                        Wyszukaj w kodzie ciągu który będzie zaznaczony na listingu poniżej:
                        <pre id="server-hint"></pre>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="info" class="col-sm-2 control-label">Dodatkowe informacje</label>
                <div class="col-sm-10">
                    <textarea class="form-control" name="link[info]" id="info" rows="4" maxlength="100"></textarea>
                </div>
            </div>
            <div class="form-group">
                <div class="col-lg-2 text-right"><strong>Opcje</strong></div>
                <div class="col-sm-10">
                    <label style="font-weight: normal;cursor:pointer"><input type="checkbox" class="checkbox-inline" name="repeat" value="true"> Dodaj ponownie w tych samych kategoriach</label><br>
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