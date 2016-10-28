<?php $view->extend('content') ?>

<?php $view['slots']->set('_sidebar', $view->render('Sidebar/content', array('current' => 'news'))) ?>

<style>
    .options label, .collection label {
        font-weight: normal;
    }

    .collection {
        padding-left: 0;
    }

    .collection .col-sm-4 {
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
    }

    .group-radio {
        cursor: pointer;
        margin-bottom: 5px;
    }

    .group-radio input[type=text] {
        border-radius: 0;
    }

    .image-preview-input {
        position: relative;
        overflow: hidden;
        margin: 0;
        color: #333;
        background-color: #fff;
        border-color: #ccc;
    }

    .image-preview-input input[type=file] {
        position: absolute;
        top: 0;
        right: 0;
        margin: 0;
        padding: 0;
        font-size: 20px;
        cursor: pointer;
        opacity: 0;
        filter: alpha(opacity=0);
    }

    .image-preview-input-title {
        margin-left: 2px;
    }

    .image-preview-input:hover {
        background-color: #e0e0e0;
        background-position: 0 -15px;
    }

    #images-list input[type=text] {
        width: 105px;
        min-width: 105px;
        max-width: 300px;
        transition: width 0.15s;
    }

    .popover-title {
        display: none;
    }
</style>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="glyphicon glyphicon-ok-sign"></i> Aktualizuj news</h3>
        <a href="<?= $app->generateUrl('news_index') ?>" class="btn btn-xs btn-danger pull-right btn-helper"><i class="fa fa-reply"></i>Wróć do listy</a>
    </div>
    <div class="panel-body">
        <form class="form-horizontal" role="form" method="post" action="<?= $app->generateUrl('news_edit', array('newsID' => $news->getId())) ?>" enctype="multipart/form-data">
            <div class="form-group">
                <label for="_name" class="col-sm-2 control-label">Tytuł</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="news[title]" id="_name" value="<?= $news->getTitle() ?>" pattern=".{1,255}" required title="Maksymalnie 255 znaków." autocomplete="off">
                </div>
            </div>
            <div class="form-group">
                <label for="_alias" class="col-sm-2 control-label">Alias</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="news[alias]" id="_alias" value="<?= $news->getAlias() ?>" pattern="[\w-]{1,255}" title="Maksimum 255 alfanumerycznych znaków." autocomplete="off" data-type="news">
                    <span class="help-block">Alias jest tworzony automatycznie na podstawie tytułu, lecz można go poprawić ręcznie.</span>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-2 text-right"><strong>Obraz</strong></div>
                <div class="col-sm-10">
                    <div class="input-group group-radio image-preview">
                        <span class="input-group-addon" style="width: 34px">
                            <input type="radio" name="news[image]" value="file" class="radio-inline">
                        </span>
                        <input type="text" class="form-control image-preview-filename" disabled="disabled">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default image-preview-clear" style="display:none;"><span class="glyphicon glyphicon-remove"></span> Usuń</button>
                            <span class="btn btn-default image-preview-input">
                                <span class="glyphicon glyphicon-folder-open"></span>
                                <span class="image-preview-input-title">Przeglądaj</span>
                                <input type="file" accept="image/png, image/jpeg, image/gif" name="image">
                            </span>
                        </span>
                    </div>
                    <div class="input-group group-radio"<?= $news->getImage() !== '000_brak.png' ? ' category-preview' : null ?>" id="images-list">
                        <span class="input-group-addon" style="width: 34px">
                            <input type="radio" name="news[image]" class="radio-inline"<?php if($news->getImage() !== '000_brak.png'): ?> value="<?= $news->getImage() ?>" checked<?php endif ?>>
                        </span>
                        <span class="input-group-btn" style="width: 105px;height: 34px">
                            <input type="text" class="form-control" data-autosize-input<?php if($news->getImage() !== '000_brak.png'): ?>  value="<?= $news->getImage() ?>"<?php endif ?>>
                            <?php if($news->getImage() !== '000_brak.png'): ?><img src="<?= $view['assets']->getUrl('kategorie/'.$news->getImage()) ?>" style="display:none;"><?php endif ?>
                        </span>
                        <span class="input-group-addon" style="width: 70px">Z listy</span>
                    </div>
                    <div class="clearfix"></div>
                    <div class="input-group group-radio category-preview">
                        <span class="input-group-addon" style="width: 34px">
                            <input type="radio" name="news[image]" value="000_brak.png" class="radio-inline"<?php if($news->getImage() == '000_brak.png'): ?> checked<?php endif ?>>
                        </span>
                        <span class="input-group-addon" style="width: 105px;height: 34px">
                            000_brak.png
                            <img src="<?= $view['assets']->getUrl('kategorie/000_brak.png') ?>" style="display:none;">
                        </span>
                        <span class="input-group-addon" style="width: 70px">Brak</span>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="_description" class="col-sm-2 control-label">Treść</label>
                <div class="col-sm-10">
                    <textarea class="form-control" name="news[description]" id="_description" rows="15" required><?= $news->getDescription() ?></textarea>
                </div>
            </div>
            <div class="form-group">
                <label for="date" class="col-sm-2 control-label">Data publikacji</label>
                <div class="col-sm-10">
                    <input type="date" class="form-control" name="news[date]" id="date" value="<?= $news->getDate() ?>" required>
                </div>
            </div>
            <div class="form-group options">
                <div class="col-lg-2 text-right"><strong>Opcje</strong></div>
                <div class="col-sm-10">
                    <label><input type="checkbox" class="checkbox-inline" name="news[comments]" value="1"<?php if($news->getComments()): ?> checked<?php endif?>> Komentarze</label>
                </div>
            </div>
            <div class="form-group">
                <div class="col-lg-2 text-right"><strong>Tagi</strong></div>
                <div class="col-sm-10 collection">
                    <?php foreach($tags as $row): ?>
                        <div class="col-sm-4">
                            <label>
                                <input type="checkbox" class="checkbox-inline" name="tags[<?= $row['id'] ?>]"<?php if(isset($_tags[$row['id']])): ?> value="<?= $_tags[$row['id']] ?>" checked<?php else: ?> value<?php endif ?>> <?= $row['name'] ?>
                            </label>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <input type="hidden" name="news[id]" value="<?= $news->getId() ?>">
                    <button type="submit" id="submit" class="btn btn-default">Wyślij</button>
                </div>
            </div>
        </form>
    </div>
</div>