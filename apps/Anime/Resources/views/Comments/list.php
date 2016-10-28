<?php if(!empty($list)): ?>
    <script>
        $(function(){
            $("#comments").find('.badge').text(<?= $total ?>);
        });
    </script>
    <?php if($total > $limit): ?>
        <a href="#comments" class="btn btn-xs btn-primary previous-comments" data-total="<?= $total ?>">Wyświetl wszystkie komentarze</a>
    <?php endif ?>
    <div class="clearfix"></div>
    <?= $view->render('Comments/previous', array('list' => $list)) ?>
<?php else: ?>
    <div class="alert alert-info fade in">
        To anime nie ma jeszcze komentarzy. Bądź pierwszym, który je skomentuje!
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    </div>
<?php endif ?>