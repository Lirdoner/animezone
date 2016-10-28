<?php $view->extend('content') ?>

<?php $view['slots']->set('_sidebar', $view->render('Sidebar/content', array('current' => 'menu'))) ?>
<?php $view['slots']->start('_footer') ?>
<script src="<?= $view['assets']->getUrl('javascript/jquery-ui-1.9.2.custom.min.js') ?>"></script>
<script>
    $(function(){
        $('.table > tbody').sortable({
            handle: '.handle',
            cursor: 'move',
            update: function(){
                var data = {};

                $(this).find('tr').each(function(position){
                    data[position] = {
                        id: $(this).attr('data-id'),
                        position: position + 1
                    };
                });

                $.ajax({
                    type: 'POST',
                    cache: false,
                    url: baseUrl + "menu/update",
                    data: {items: data}
                });
            }
        });

        $('#link').on('change keydown keyup', function(){
            var submit = $('#submit');
            var that = $(this);

            if(that.val() != '#')
            {
                $.ajax({
                    url: '<?= $app->basePath('') ?>' + $(this).val(),
                    success: function(){
                        that.parents('.form-group').removeClass('has-error');
                        submit.prop('disabled', false);
                    },
                    error: function(){
                        that.parents('.form-group').addClass('has-error');
                        submit.prop('disabled', true);
                    }
                });
            }
        });

        $('#icon').on('change keydown keyup', function(event){
            var target = $('#_icon');

            if(event.keyCode != 8)
            {
                target.addClass($(this).val());
            } else
            {
                target.removeAttr('class');
            }
        });
    });
</script>
<?php $view['slots']->stop() ?>

<div class="panel panel-default category-description">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="glyphicon glyphicon-ok-sign"></i> Aktualizuj Link</h3>
        <a href="<?= $app->generateUrl('menu_index') ?>" class="btn btn-xs btn-danger pull-right btn-helper"><i class="fa fa-reply"></i>Wróć do listy</a>
    </div>
    <div class="panel-body">
        <form class="form-horizontal" role="form" method="post" action="<?= $app->generateUrl('menu_edit', array('menuID' => $menu->getId())) ?>">
            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">Tytuł</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="menu[name]" id="name" value="<?= $menu->getName() ?>" pattern=".{1,255}" required title="Maksymalnie 255 znaków." autocomplete="off">
                </div>
            </div>
            <div class="form-group">
                <label for="typ" class="col-sm-2 control-label">Typ</label>
                <div class="col-sm-10">
                    <select class="form-control" id="typ" name="menu[parent_id]">
                        <option value="0"<?= !$menu->getParentId() ? ' selected' : null ?>>Link główny</option>
                        <optgroup label="Submenu">
                            <?php foreach($list as $row): ?>
                                <option value="<?= $row['id'] ?>"<?= $menu->getParentId() == $row['id'] ? ' selected' : null ?>><?= $row['name'] ?></option>
                            <?php endforeach ?>
                        </optgroup>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="link" class="col-sm-2 control-label">Adres</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="menu[link]" id="link" value="<?= $menu->getLink() ?>" pattern=".{1,255}" required title="Maksymalnie 255 znaków." autocomplete="off">
                    <span class="help-block">Adres musi istnieć oraz być poprzedzony <kbd>/</kbd> ukośnikiem. Ustaw znak hash <kbd>#</kbd> w przypadku kiedy nasz link ma posiadać submenu.</span>
                </div>
            </div>
            <div class="form-group">
                <label for="icon" class="col-sm-2 control-label">Ikona</label>
                <div class="col-sm-10">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="<?= $menu->getIcon() ?>" id="_icon" style="margin:0;font-size:16px"></i></span>
                        <input type="text" class="form-control" name="menu[icon]" id="icon" value="<?= $menu->getIcon() ?>" pattern=".{1,50}" title="Maksymalnie 50 znaków." autocomplete="off">
                    </div>
                    <span class="help-block">Może być to <a href="http://fortawesome.github.io/Font-Awesome/icons/#new" target="_blank">Font-Awesome</a> lub <a href="http://getbootstrap.com/components/#glyphicons-glyphs" target="_blank">Glyphicon</a>.</span>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <input type="hidden" name="menu[id]" value="<?= $menu->getId() ?>">
                    <button type="submit" id="submit" class="btn btn-default">Wyślij</button>
                </div>
            </div>
        </form>
    </div>
    <?php if(!empty($submenu)): ?>
        <h5>Submenu</h5>
        <table class="table table-bordered table-striped table-hover" style="margin-bottom: 0">
            <thead>
            <tr>
                <th colspan="2">Nazwa</th>
                <th class="text-center col-sm-1">Akcje</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($submenu as $row): ?>
                <tr data-id="<?= $row['id'] ?>" id="id_<?= $row['id'] ?>">
                    <td class="text-center handle">
                        <i class="glyphicon glyphicon-resize-vertical"></i>
                    </td>
                    <td class="col-sm-11">
                        <a href="<?= $app->generateUrl('menu_edit', array('menuID' => $row['id'])) ?>"><i class="<?= $row['icon'] ?>" style="font-size:16px"></i><?= $row['name'] ?></a>
                    </td>
                    <td class="text-center" style="padding: 8px 0 0 0">
                        <div class="btn-group btn-group-xs">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                Akcje <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu text-left" role="menu">
                                <li><a href="<?= $app->generateUrl('menu_edit', array('menuID' => $row['id'])) ?>"><i class="fa fa-edit"></i> Edytuj</a></li>
                                <li><a href="<?= $app->generateUrl('menu_delete', array('menuID' => $row['id'])) ?>"><i class="fa fa-times"></i> Usuń</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    <?php endif ?>
</div>