<?= '<?xml version="1.0" encoding="utf-8" ?>'.PHP_EOL ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>Nowości na AnimeZone.pl</title>
        <atom:link href="<?= $app->getRequest()->getSchemeAndHttpHost().$app->getRequest()->getRequestUri() ?>" rel="self" type="application/rss+xml" />
        <link><?= $app->getRequest()->getSchemeAndHttpHost() ?></link>
        <description>AnimeZone.pl - Najnowsze odcinki <?= $lang == 'pl' ? strtoupper($lang) : null; ?></description>
        <language>pl</language>
        <pubDate><?= $date ?></pubDate>

        <?php foreach($items as $item): ?>

        <item>
            <title><?= htmlspecialchars($item['name']) ?> odcinek <?= $item['number'] ?>: <?= $item['title'] ?></title>
            <link><?= $app->generateUrl('episodes_cat', array('cat' => $item['alias']), true) ?></link>
            <description>
                <![CDATA[<img src="http://<?= $app->getRequest()->getHost().$view['assets']->getUrl('kategorie/'.$item['image']) ?>" align="left" vspace="4" hspace="4" />]]>
            </description>
            <category>Nowości</category>
            <pubDate><?= date_format(date_create($item['date_add']), 'D, d M Y H:i:s').' GMT' ?></pubDate>
            <guid><?= $app->generateUrl('episodes_cat', array('cat' => $item['alias']), true) ?></guid>
        </item>

        <?php endforeach ?>

    </channel>
</rss>