<?php $view->extend('content') ?>

<?php $view['slots']->set('_sidebar', $view->render('Sidebar/content', array('current' => 'menu'))) ?>
<?php $view['slots']->start('_footer') ?>
<script>
    $(function(){
        $('#link').on('change keydown keyup', function(){
            var submit = $('#submit');
            var that = $(this);
            var reg = new RegExp('^http');

            if(that.val() != '#' && !reg.test(that.val()))
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

        $('#icon').on('change keydown keyup', function(){
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

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="glyphicon glyphicon-plus-sign"></i> Nowy Link</h3>
        <a href="<?= $app->generateUrl('menu_index') ?>" class="btn btn-xs btn-danger pull-right btn-helper"><i class="fa fa-reply"></i>Wróć do listy</a>
    </div>
    <div class="panel-body">
        <form class="form-horizontal" role="form" method="post" action="<?= $app->generateUrl('menu_create') ?>">
            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">Tytuł</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="menu[name]" id="name" placeholder="Nazwa przycisku" pattern=".{1,255}" required title="Maksymalnie 255 znaków." autocomplete="off">
                </div>
            </div>
            <div class="form-group">
                <label for="parent" class="col-sm-2 control-label">Rodzic</label>
                <div class="col-sm-10">
                    <select class="form-control" id="parent" name="menu[parent_id]">
                        <option value="0">Link główny</option>
                        <optgroup label="Submenu">
                            <?php foreach($list as $row): ?>
                                <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                            <?php endforeach ?>
                        </optgroup>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="link" class="col-sm-2 control-label">Adres</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="menu[link]" id="link" placeholder="Link" pattern=".{1,255}" required title="Maksymalnie 255 znaków." autocomplete="off">
                    <span class="help-block">Adres musi istnieć oraz być poprzedzony <kbd>/</kbd> ukośnikiem. Ustaw znak hash <kbd>#</kbd> w przypadku kiedy nasz link ma posiadać submenu.</span>
                </div>
            </div>
            <div class="form-group">
                <label for="icon" class="col-sm-2 control-label">Ikona</label>
                <div class="col-sm-10">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="" id="_icon" style="margin:0;font-size:16px"></i></span>
                        <input type="text" class="form-control" name="menu[icon]" id="icon" placeholder="Ikona" pattern=".{1,50}" title="Maksymalnie 50 znaków." autocomplete="off">
                    </div>
                    <span class="help-block">Może być to <a href="http://fortawesome.github.io/Font-Awesome/icons/#new" target="_blank">Font-Awesome</a> lub <a href="http://getbootstrap.com/components/#glyphicons-glyphs" target="_blank">Glyphicon</a>.</span>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" id="submit" class="btn btn-default">Wyślij</button>
                </div>
            </div>
        </form>
    </div>
</div>