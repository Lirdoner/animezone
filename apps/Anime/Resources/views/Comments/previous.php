<?php foreach(array_reverse($list) as $comment): ?>
    <div class="comment">
        <div class="avatar pull-left">
            <a href="<?= $app->generateUrl('user_profile', array('user_name' => $comment['name'])) ?>">
                <img src="<?= $view['text']->avatar($comment['avatar'], $view['assets']) ?>" class="img-responsive img-thumbnail">
            </a>
        </div>
        <div class="comment-body pull-left">
            <p class="comment-info">
                @<a href="<?= $app->generateUrl('user_profile', array('user_name' => $comment['name'])) ?>"<?= $comment['role'] == 'ROLE_ADMIN' ? ' class="text-danger"' : null ?>><strong><?= $comment['name'] ?></strong></a> &#8226;
                <small title="<?= $view['text']->timeElapsed($comment['date'], true) ?>"><?= $view['text']->timeElapsed($comment['date']) ?></small> &#8226; <a href="#" data-report="<?= $comment['id'] ?>" class="comment-report">zgłoś nadużycie</a>
                <?php if($app->getUser()->getId() == $comment['user_id'] || $app->getUser()->isAdmin()): ?>
                    &#8226; <a href="#" data-edit="<?= $comment['id'] ?>" class="comment-edit">edytuj</a>
                    <button type="button" class="close comment-delete" data-delete="<?= $comment['id'] ?>"><span aria-hidden="true">&times;</span><span class="mobile"> usuń</span></button>
                <?php endif ?>
            </p>
            <p class="comment-message">
                <?= $view['text']->bbcode($comment['message']) ?>
            </p>
        </div>
        <div class="clearfix"></div>
    </div>
<?php endforeach ?>