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

        $('.dropdown-menu').sortable({
            cursor: 'move',
            update: function(){
                var data = {};

                $(this).find('li').each(function(position){
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
    });
</script>
<?php $view['slots']->stop() ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-slack"></i> Menu</h3>
        <a href="<?= $app->generateUrl('menu_create') ?>" class="btn btn-xs btn-success pull-right btn-helper"><i class="fa fa-plus-square"></i>Utwórz nowy  link</a>
    </div>
    <?php if(!empty($list)): ?>
        <table class="table table-bordered table-striped table-hover" style="margin-bottom: 0">
            <thead>
            <tr>
                <th colspan="2">Nazwa</th>
                <th class="text-center col-sm-1">Akcje</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($list as $row): ?>
                <tr data-id="<?= $row['id'] ?>" id="id_<?= $row['id'] ?>">
                    <td class="text-center handle">
                        <i class="glyphicon glyphicon-resize-vertical"></i>
                    </td>
                    <td class="col-sm-11"><a href="<?= $app->generateUrl('menu_edit', array('menuID' => $row['id'])) ?>">
                        <i class="<?= $row['icon'] ?>" style="font-size:16px"></i><?= $row['name'] ?></a>
                        <?php if(!empty($row['submenu'])): ?>
                            <div class="btn-group pull-right">
                                <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                                    Submenu <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <?php foreach($row['submenu'] as $i => $subitem): ?>
                                        <li data-id="<?= $subitem['id'] ?>"><a href="<?= $app->generateUrl('menu_edit', array('menuID' => $subitem['id'])) ?>"><i class="<?= $subitem['icon'] ?>"></i> <?= $subitem['name'] ?></a></li>
                                    <?php endforeach ?>
                                </ul>
                            </div>
                        <?php endif ?>
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
    <?php else: ?>
        <div class="panel-body">
            <div class="alert alert-info fade in" style="margin-bottom: 0">
                Brak pozycji do wyświetlenia.<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            </div>
        </div>
    <?php endif ?>
</div>