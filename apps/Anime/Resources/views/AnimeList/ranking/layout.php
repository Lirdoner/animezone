<?php $view->extend('content') ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="glyphicon glyphicon-signal"></i> Ranking <?php $view['slots']->output('_headline') ?></h3>
    </div>
    <div class="panel-body">
        <div class="bs-callout bs-callout-info">
            <p>
                Wynik możesz sortować klikając w nagłówki tabel. Wskaźnik <span class="dropdown"><span class="caret"></span></span> oznacza sortowanie od najwyższego, <br>
                odpowiednio <span class="dropup"><span class="caret"></span></span> oznacza sortowanie od najniższego wyniku. Kolumna z sortowaniem jest podświetlona.
            </p>
        </div>
    </div>

    <?php $view['slots']->output('_content') ?>

</div>