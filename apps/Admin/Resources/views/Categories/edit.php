<?php $view->extend('content') ?>

<?php $view['slots']->set('_sidebar_addon', $view->render('Sidebar/categories/search')) ?>
<?php $view['slots']->set('_sidebar', $view->render('Sidebar/categories/layout', array('current' => 'categories'))) ?>

<style>
    .collection {
        padding-left: 0;
    }

    .collection .col-sm-4 {
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
    }

    .collection label {
        font-weight: normal;
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
        <h3 class="panel-title"><i class="glyphicon glyphicon-ok-sign"></i> Aktualizuj kategorie</h3>
        <a href="<?= $app->generateUrl('categories_index') ?>" class="btn btn-xs btn-danger pull-right btn-helper"><i class="fa fa-reply"></i>Wróć do listy</a>
    </div>
    <div class="panel-body">
        <form class="form-horizontal" role="form" method="post" action="<?= $app->generateUrl('categories_edit', array('catID' => $category->getId())) ?>" enctype="multipart/form-data">
            <div class="form-group">
                <label for="_name" class="col-sm-2 control-label">Nazwa</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="category[name]" id="_name" value="<?= $category->getName() ?>" placeholder="Tytuł anime" pattern=".{1,100}" required title="Maksimum 100 znaków." autocomplete="off">
                </div>
            </div>
            <div class="form-group">
                <label for="_alternate" class="col-sm-2 control-label">Alternatywny</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="category[alternate]" id="_alternate" value="<?= $category->getAlternate() ?>" placeholder="Alternatywny tytuł anime" pattern=".{1,100}" title="Maksimum 100 znaków." autocomplete="off">
                </div>
            </div>
            <div class="form-group">
                <label for="_alias" class="col-sm-2 control-label">Alias</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="category[alias]" id="_alias" value="<?= $category->getAlias() ?>" pattern="[\w-]{1,100}" title="Maksimum 100 alfanumerycznych znaków." autocomplete="off" data-type="categories">
                    <span class="help-block">Alias jest tworzony automatycznie na podstawie nazwy, lecz można go poprawić ręcznie.</span>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-2 text-right"><strong>Obraz</strong></div>
                <div class="col-sm-10">
                    <div class="input-group group-radio image-preview">
                        <span class="input-group-addon" style="width: 34px">
                            <input type="radio" name="category[image]" value="file" class="radio-inline">
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
                    <div class="input-group group-radio<?= $category->getImage() !== '000_brak.png' ? ' category-preview' : null ?>" id="images-list">
                        <span class="input-group-addon" style="width: 34px">
                            <input type="radio" name="category[image]" class="radio-inline"<?php if($category->getImage() !== '000_brak.png'): ?> value="<?= $category->getImage() ?>" checked<?php endif ?>>
                        </span>
                        <span class="input-group-btn" style="width: 105px;height: 34px">
                            <input type="text" class="form-control" data-autosize-input<?php if($category->getImage() !== '000_brak.png'): ?>  value="<?= $category->getImage() ?>"<?php endif ?>>
                            <?php if($category->getImage() !== '000_brak.png'): ?><img src="<?= $view['assets']->getUrl('kategorie/'.$category->getImage()) ?>" style="display:none;"><?php endif ?>
                        </span>
                        <span class="input-group-addon" style="width: 70px">Z listy</span>
                    </div>
                    <div class="clearfix"></div>
                    <div class="input-group group-radio category-preview">
                        <span class="input-group-addon" style="width: 34px">
                            <input type="radio" name="category[image]" value="000_brak.png" class="radio-inline"<?php if($category->getImage() == '000_brak.png'): ?> checked<?php endif ?>>
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
                <label for="_letter" class="col-sm-2 control-label">Litera</label>
                <div class="col-sm-10">
                    <select class="form-control" id="_letter" name="category[letter]">
                        <?php foreach($_letters as $letter): ?>
                            <option<?= $letter == $category->getLetter() ? ' selected' : null ?>><?= $letter ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="_description" class="col-sm-2 control-label">Opis</label>
                <div class="col-sm-10">
                    <textarea class="form-control" name="category[description]" id="_description" rows="9" required><?= $category->getDescription() ?></textarea>
                </div>
            </div>
            <div class="form-group">
                <label for="_pegi" class="col-sm-2 control-label">Wiek widowni</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="category[pegi]" id="_pegi" value="<?= $category->getPegi() ?>" pattern=".{1,20}" title="Maksimum 20 znaków.">
                </div>
            </div>
            <div class="form-group">
                <label for="_year" class="col-sm-2 control-label">Rok produkcji</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" name="category[year]" id="_year" value="<?= $category->getYear() ?>" pattern="\d{4}" required>
                </div>
            </div>
            <div class="form-group">
                <label for="_season" class="col-sm-2 control-label">Sezon</label>
                <div class="col-sm-10">
                    <select class="form-control" id="_season" name="category[season]">
                        <option value="0"<?= 0 == $category->getSeason() ? ' selected' : null ?>>- Brak -</option>
                        <?php foreach($_season as $i => $v): ?>
                            <option value="<?= $i ?>"<?= $i == $category->getSeason() ? ' selected' : null ?>><?= $v ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="_status" class="col-sm-2 control-label">Status</label>
                <div class="col-sm-10">
                    <select class="form-control" id="_status" name="category[status]">
                        <?php foreach($_status as $i => $v): ?>
                            <option value="<?= $i ?>"<?= $i == $category->getStatus() ? ' selected' : null ?>><?= $v ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="_release" class="col-sm-2 control-label">Rodzaj</label>
                <div class="col-sm-10">
                    <select class="form-control" id="_release" name="category[release]">
                        <?php foreach($_release as $i => $v): ?>
                            <option value="<?= $i ?>"<?= $i == $category->getRelease() ? ' selected' : null ?>><?= $v ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="_series" class="col-sm-2 control-label">Seria</label>
                <div class="col-sm-10">
                    <select class="form-control" id="_series" name="category[series]">
                        <option value="0">- Brak -</option>
                        <?php foreach($series as $row): ?>
                            <option value="<?= $row['id'] ?>"<?= $row['id'] == $category->getSeries() ? ' selected' : null ?>><?= $row['name'] ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <div class="col-lg-2 text-right"><strong>Typ widowni</strong></div>
                <div class="col-sm-10 collection">
                    <?php foreach($types as $row): ?>
                        <div class="col-sm-4">
                            <label>
                                <input type="checkbox" class="checkbox-inline" name="types[<?= $row['id'] ?>]"<?php if(isset($_types[$row['id']])): ?> value="<?= $_types[$row['id']] ?>" checked<?php else: ?> value<?php endif ?>> <?= $row['name'] ?>
                            </label>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
            <div class="form-group">
                <div class="col-lg-2 text-right"><strong>Gatunki</strong></div>
                <div class="col-sm-10 collection">
                    <?php foreach($species as $row): ?>
                        <div class="col-sm-4">
                            <label>
                                <input type="checkbox" class="checkbox-inline" name="species[<?= $row['id'] ?>]"<?php if(isset($_species[$row['id']])): ?> value="<?= $_species[$row['id']] ?>" checked<?php else: ?> value<?php endif ?>> <?= $row['name'] ?>
                            </label>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
            <div class="form-group">
                <div class="col-lg-2 text-right"><strong>Tematyka</strong></div>
                <div class="col-sm-10 collection">
                    <?php foreach($topics as $row): ?>
                        <div class="col-sm-4">
                            <label>
                                <input type="checkbox" class="checkbox-inline" name="topics[<?= $row['id'] ?>]"<?php if(isset($_topics[$row['id']])): ?> value="<?= $_topics[$row['id']] ?>" checked<?php else: ?> value<?php endif ?>> <?= $row['name'] ?>
                            </label>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <input type="hidden" name="category[id]" value="<?= $category->getId() ?>">
                    <button type="submit" class="btn btn-default" id="submit">Aktualizuj</button>
                </div>
            </div>
        </form>
    </div>
</div>