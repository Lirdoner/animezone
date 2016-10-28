<?php

return array(
    'homepage' => array(
        'path' => '/{lang}',
        'defaults' => array('_controller' => 'Anime:Home:index', 'lang' => 'all'),
        'requirements' => array('lang' => 'pl|all'),
    ),
    'anime_list_by_type' => array(
        'path' => '/anime/{type}/{letter}',
        'defaults' => array('_controller' => 'Anime:AnimeList:type', 'letter' => 0),
        'requirements' => array('type' => 'lista|filmy', 'letter' => '[A-Z|0-9]'),
    ),
    'anime_list_by_status' => array(
        'path' => '/anime/{status}',
        'defaults' => array('_controller' => 'Anime:AnimeList:status'),
        'requirements' => array('status' => 'nadchodzace|zakonczone')
    ),
    'anime_rate_ranking' => array(
        'path' => '/anime/ranking/ocen/{type}',
        'defaults' => array('_controller' => 'Anime:AnimeList:rateRanking', 'type' => 'anime'),
        'requirements' => array('type' => 'anime|filmy'),
    ),
    'anime_fan_ranking' => array(
        'path' => '/anime/ranking/fanow/{type}',
        'defaults' => array('_controller' => 'Anime:AnimeList:fansRanking', 'type' => 'anime'),
        'requirements' => array('type' => 'anime|filmy'),
    ),
    'anime_views_ranking' => array(
        'path' => '/anime/ranking/wyswietlen/{type}',
        'defaults' => array('_controller' => 'Anime:AnimeList:viewsRanking', 'type' => 'anime'),
        'requirements' => array('type' => 'anime|filmy'),
    ),
    'anime_watch_ranking' => array(
        'path' => '/anime/ranking/{type}',
        'defaults' => array('_controller' => 'Anime:AnimeList:watchRanking', 'type' => 'anime'),
        'requirements' => array('type' => 'anime|filmow'),
    ),
    'anime_season' => array(
        'path' => '/anime/sezony/{year}/{season}',
        'defaults' => array('_controller' => 'Anime:AnimeList:season', 'year' => 'current', 'season' => 'current'),
        'requirements' => array('year' => '[\d]{4}|current', 'season' => 'wiosna|lato|jesien|zima|current'),
    ),
    'report_comment' => array(
        'path' => '/report/comment',
        'defaults' => array('_controller' => 'Anime:Report:comment'),
        'requirements' => array('_method' => 'POST'),
    ),
    'report_link' => array(
        'path' => '/report/link',
        'defaults' => array('_controller' => 'Anime:Report:link'),
        'requirements' => array('_method' => 'POST'),
    ),
    'anime_species' => array(
        'path' => '/gatunki',
        'defaults' => array('_controller' => 'Anime:Species:index'),
    ),
    'search' => array(
        'path' => '/szukaj',
        'defaults' => array('_controller' => 'Anime:Search:index'),
    ),
    'episodes_watched' => array(
        'path' => '/odcinki-online/{cat}/watched/{type}',
        'defaults' => array('_controller' => 'Anime:Episodes:watched'),
        'requirements' => array('cat' => '[\w-]+', 'type' => '[1-5]'),
    ),
    'episodes_rating' => array(
        'path' => '/odcinki-online/{cat}/rating/{value}',
        'defaults' => array('_controller' => 'Anime:Episodes:rating'),
        'requirements' => array('cat' => '[\w-]+', 'value' => '[1-9]|10|delete'),
    ),
    'episodes_favorite' => array(
        'path' => '/odcinki-online/{cat}/favorite',
        'defaults' => array('_controller' => 'Anime:Episodes:favorite'),
        'requirements' => array('cat' => '[\w-]+'),
    ),
    'episodes_show' => array(
        'path' => '/odcinki-online/{cat}/{id}',
        'defaults' => array('_controller' => 'Anime:Episodes:show'),
        'requirements' => array('cat' => '[\w-]+', 'id' => '[\d]+', '_method' => 'GET'),
    ),
    'episodes_show_link' => array(
        'path' => '/odcinki-online/{cat}/{id}',
        'defaults' => array('_controller' => 'Anime:Episodes:showLink'),
        'requirements' => array('cat' => '[\w-]+', 'id' => '[\d]+', '_method' => 'POST'),
    ),
    'episodes_show_combined_link' => array(
        'path' => '/odcinki-online/{id}/vk',
        'defaults' => array('_controller' => 'Anime:Episodes:showCombinedLink'),
        'requirements' => array('id' => '[\d]+', '_method' => 'GET'),
    ),
    'episodes_cat' => array(
        'path' => '/odcinki-online/{cat}',
        'defaults' => array('_controller' => 'Anime:Episodes:category'),
        'requirements' => array('cat' => '[\w-]+'),
    ),
    'add_episode' => array(
        'path' => '/dodaj',
        'defaults' => array('_controller' => 'Anime:Episodes:addNew'),
    ),
    'faq' => array(
        'path' => '/faq',
        'defaults' => array('_controller' => 'Anime:Faq:index'),
    ),
    'contact' => array(
        'path' => '/kontakt',
        'defaults' => array('_controller' => 'Anime:Contact:index'),
    ),
    'news' => array(
        'path' => '/nowosci/{page}',
        'defaults' => array('_controller' => 'Anime:News:index', 'page' => 1),
        'requirements' => array('page' => '[\d]+|_PAGE_'),
    ),
    'news_tags' => array(
        'path' => '/news/tags/{tagID}',
        'defaults' => array('_controller' => 'Anime:News:tags'),
        'requirements' => array('tagID' => '[\d]+'),
    ),
    'news_show' => array(
        'path' => '/news/{slug}',
        'defaults' => array('_controller' => 'Anime:News:show'),
        'requirements' => array('slug' => '[\w-]+'),
    ),
    'login' => array(
        'path' => '/login',
        'defaults' => array('_controller' => 'Anime:Auth:login'),
        'requirements' => array('_method' => 'POST'),
    ),
    'login_mobile' => array(
        'path' => '/login',
        'defaults' => array('_controller' => 'Anime:Auth:loginView'),
        'requirements' => array('_method' => 'GET'),
    ),
    'login_facebook' => array(
        'path' => '/login/facebook',
        'defaults' => array('_controller' => 'Anime:Auth:loginFacebook'),
    ),
    'logout' => array(
        'path' => '/logout',
        'defaults' => array('_controller' => 'Anime:Auth:logout'),
    ),
    'security_image' => array(
        'path' => '/images/statistics.gif',
        'defaults' => array('_controller' => 'Anime:Security:statistics'),
    ),
    'pages' => array(
        'path' => '/strony/{alias}',
        'defaults' => array('_controller' => 'Anime:Pages:show'),
        'requirements' => array('alias' => '[\w-]+')
    ),
    'user_edit_profile' => array(
        'path' => '/user/edit',
        'defaults' => array('_controller' => 'Anime:User:edit'),
    ),
    'user_register' => array(
        'path' => '/user/register',
        'defaults' => array('_controller' => 'Anime:User:register'),
    ),
    'user_register_resend' => array(
        'path' => '/user/register/resend',
        'defaults' => array('_controller' => 'Anime:User:registerResend'),
    ),
    'user_register_confirm' => array(
        'path' => '/user/register/{code}/{email}',
        'defaults' => array('_controller' => 'Anime:User:registerConfirm'),
        'requirements' => array('code' => '[\w]{64}', 'email' => '.+'),
    ),
    'user_restore' => array(
        'path' => '/user/restore',
        'defaults' => array('_controller' => 'Anime:User:restore'),
    ),
    'user_restore_confirm' => array(
        'path' => '/user/restore/{code}',
        'defaults' => array('_controller' => 'Anime:User:restoreConfirm'),
        'requirements' => array('code' => '[\w]{64}'),
    ),
    'user_profile_details' => array(
        'path' => '/user/{user_name}/{action}/details',
        'defaults' => array('_controller' => 'Anime:User:profileDetails'),
        'requirements' => array('user_name' => '[\w-\.]{1,32}', 'action' => 'favorites|rated|commented|watching|watched|plans|stopped|abandoned'),
    ),
    'user_profile' => array(
        'path' => '/user/{user_name}/{action}',
        'defaults' => array('_controller' => 'Anime:User:profile', 'action' => 'favorites'),
        'requirements' => array('user_name' => '[\w-\.]{1,32}', 'action' => 'favorites|rated|watching|watched|plans|stopped|abandoned'),
    ),
    'admin' => array(
        'path' => '/backend.php',
        'defaults' => array('_controller' => '_'),
    ),
    'comments_list' => array(
        'path' => '/comments/list',
        'defaults' => array('_controller' => 'Anime:Comments:list'),
        'requirements' => array('_method' => 'POST'),
    ),
    'comments_previous' => array(
        'path' => '/comments/previous',
        'defaults' => array('_controller' => 'Anime:Comments:previous'),
        'requirements' => array('_method' => 'POST'),
    ),
    'comments_update' => array(
        'path' => '/comments/update',
        'defaults' => array('_controller' => 'Anime:Comments:update'),
        'requirements' => array('_method' => 'POST'),
    ),
    'comments_edit' => array(
        'path' => '/comments/edit',
        'defaults' => array('_controller' => 'Anime:Comments:edit'),
        'requirements' => array('_method' => 'POST'),
    ),
    'comments_delete' => array(
        'path' => '/comments/delete',
        'defaults' => array('_controller' => 'Anime:Comments:delete'),
        'requirements' => array('_method' => 'POST'),
    ),
    'comments_redirect' => array(
        'path' => '/comments/redirect/{commentID}',
        'defaults' => array('_controller' => 'Anime:Comments:redirect'),
        'requirements' => array('commentID' => '[\d]+'),
    ),
    '_captcha' => array(
        'path' => '/_captcha',
        'defaults' => array('_controller' => 'Anime:Captcha:generate'),
    ),
);
