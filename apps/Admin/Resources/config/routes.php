<?php

return array(
    'dashboard' => array(
        'path' => '/',
        'defaults' => array('_controller' => 'Admin:Dashboard:index'),
    ),
    'dashboard_note' => array(
        'path' => '/note',
        'defaults' => array('_controller' => 'Admin:Dashboard:updateNote'),
        'requirements' => array('_method' => 'POST'),
    ),
    'dashboard_logs' => array(
        'path' => '/logs',
        'defaults' => array('_controller' => 'Admin:Dashboard:logs'),
    ),
    'login' => array(
        'path' => '/login',
        'defaults' => array('_controller' => 'Admin:Auth:login'),
    ),
    'logout' => array(
        'path' => '/logout',
        'defaults' => array('_controller' => 'Admin:Auth:logout'),
    ),
    'categories_index' => array(
        'path' => '/categories',
        'defaults' => array('_controller' => 'Admin:Categories:index'),
    ),
    'categories_create' => array(
        'path' => '/categories/create',
        'defaults' => array('_controller' => 'Admin:Categories:create'),
    ),
    'categories_edit' => array(
        'path' => '/categories/{catID}/edit',
        'defaults' => array('_controller' => 'Admin:Categories:edit'),
        'requirements' => array('catID' => '\d+'),
    ),
    'categories_update' => array(
        'path' => '/categories/update',
        'defaults' => array('_controller' => 'Admin:Categories:update'),
        'requirements' => array('_method' => 'POST'),
    ),
    'categories_delete' => array(
        'path' => '/categories/{catID}/delete',
        'defaults' => array('_controller' => 'Admin:Categories:delete'),
        'requirements' => array('catID' => '\d+'),
    ),
    'categories_search' => array(
        'path' => '/categories/search',
        'defaults' => array('_controller' => 'Admin:Categories:search'),
    ),
    'categories_alias' => array(
        'path' => '/categories/alias',
        'defaults' => array('_controller' => 'Admin:Categories:alias'),
        'requirements' => array('_method' => 'POST'),
    ),
    'categories_images' => array(
        'path' => '/categories/images',
        'defaults' => array('_controller' => 'Admin:Categories:images'),
        'requirements' => array('_method' => 'POST'),
    ),
    'categories_list' => array(
        'path' => '/categories/list',
        'defaults' => array('_controller' => 'Admin:Categories:list'),
        'requirements' => array('_method' => 'POST'),
    ),
    'categories_stats' => array(
        'path' => '/categories/stats',
        'defaults' => array('_controller' => 'Admin:Categories:stats'),
        'requirements' => array('_method' => 'POST'),
    ),
    'species_index' => array(
        'path' => '/species',
        'defaults' => array('_controller' => 'Admin:Species:index'),
    ),
    'species_create' => array(
        'path' => '/species/create',
        'defaults' => array('_controller' => 'Admin:Species:create'),
    ),
    'species_edit' => array(
        'path' => '/species/{speciesID}/edit',
        'defaults' => array('_controller' => 'Admin:Species:edit'),
        'requirements' => array('speciesID' => '\d+'),
    ),
    'species_update' => array(
        'path' => '/species/update',
        'defaults' => array('_controller' => 'Admin:Species:update'),
        'requirements' => array('_method' => 'POST'),
    ),
    'species_delete' => array(
        'path' => '/species/{speciesID}/delete',
        'defaults' => array('_controller' => 'Admin:Species:delete'),
        'requirements' => array('speciesID' => '\d+'),
    ),
    'topics_index' => array(
        'path' => '/topics',
        'defaults' => array('_controller' => 'Admin:Topics:index'),
    ),
    'topics_create' => array(
        'path' => '/topics/create',
        'defaults' => array('_controller' => 'Admin:Topics:create'),
    ),
    'topics_edit' => array(
        'path' => '/topics/{topicID}/edit',
        'defaults' => array('_controller' => 'Admin:Topics:edit'),
        'requirements' => array('topicID' => '\d+'),
    ),
    'topics_update' => array(
        'path' => '/topics/update',
        'defaults' => array('_controller' => 'Admin:Topics:update'),
        'requirements' => array('_method' => 'POST'),
    ),
    'topics_delete' => array(
        'path' => '/topics/{topicID}/delete',
        'defaults' => array('_controller' => 'Admin:Topics:delete'),
    ),
    'types_index' => array(
        'path' => '/types',
        'defaults' => array('_controller' => 'Admin:Types:index'),
    ),
    'types_create' => array(
        'path' => '/types/create',
        'defaults' => array('_controller' => 'Admin:Types:create'),
    ),
    'types_edit' => array(
        'path' => '/types/{typeID}/edit',
        'defaults' => array('_controller' => 'Admin:Types:edit'),
        'requirements' => array('typeID' => '\d+'),
    ),
    'types_update' => array(
        'path' => '/types/update',
        'defaults' => array('_controller' => 'Admin:Types:update'),
        'requirements' => array('_method' => 'POST'),
    ),
    'types_delete' => array(
        'path' => '/types/{typeID}/delete',
        'defaults' => array('_controller' => 'Admin:Types:delete'),
        'requirements' => array('typeID' => '\d+'),
    ),
    'series_index' => array(
        'path' => '/series',
        'defaults' => array('_controller' => 'Admin:Series:index'),
    ),
    'series_create' => array(
        'path' => '/series/create',
        'defaults' => array('_controller' => 'Admin:Series:create'),
    ),
    'series_edit' => array(
        'path' => '/series/{rowID}/edit',
        'defaults' => array('_controller' => 'Admin:Series:edit'),
        'requirements' => array('rowID' => '\d+'),
    ),
    'series_update' => array(
        'path' => '/series/update',
        'defaults' => array('_controller' => 'Admin:Series:update'),
        'requirements' => array('_method' => 'POST'),
    ),
    'series_delete' => array(
        'path' => '/series/{rowID}/delete',
        'defaults' => array('_controller' => 'Admin:Series:delete'),
        'requirements' => array('rowID' => '\d+'),
    ),
    'episodes_index' => array(
        'path' => '/episodes',
        'defaults' => array('_controller' => 'Admin:Episodes:index'),
    ),
    'episodes_create' => array(
        'path' => '/episodes/create',
        'defaults' => array('_controller' => 'Admin:Episodes:create'),
    ),
    'episodes_edit' => array(
        'path' => '/episodes/{episodeID}/edit',
        'defaults' => array('_controller' => 'Admin:Episodes:edit'),
        'requirements' => array('episodeID' => '\d+'),
    ),
    'episodes_update' => array(
        'path' => '/episodes/update',
        'defaults' => array('_controller' => 'Admin:Episodes:update'),
        'requirements' => array('_method' => 'POST'),
    ),
    'episodes_delete' => array(
        'path' => '/episodes/{episodeID}/delete',
        'defaults' => array('_controller' => 'Admin:Episodes:delete'),
        'requirements' => array('episodeID' => '\d+'),
    ),
    'episodes_search' => array(
        'path' => '/episodes/search',
        'defaults' => array('_controller' => 'Admin:Episodes:search'),
    ),
    'episodes_check' => array(
        'path' => '/episodes/check',
        'defaults' => array('_controller' => 'Admin:Episodes:check'),
        'requirements' => array('_method' => 'POST'),
    ),
    'episodes_list' => array(
        'path' => '/episodes/list',
        'defaults' => array('_controller' => 'Admin:Episodes:list'),
        'requirements' => array('_method' => 'POST'),
    ),
    'episodes_stats' => array(
        'path' => '/episodes/stats',
        'defaults' => array('_controller' => 'Admin:Episodes:stats'),
        'requirements' => array('_method' => 'POST'),
    ),
    'episodes_reload' => array(
        'path' => '/episodes/reload/{episodeID}',
        'defaults' => array('_controller' => 'Admin:Episodes:reload'),
        'requirements' => array('episodeID' => '\d+'),
    ),
    'links_index' => array(
        'path' => '/links',
        'defaults' => array('_controller' => 'Admin:Links:index'),
    ),
    'links_create' => array(
        'path' => '/links/create',
        'defaults' => array('_controller' => 'Admin:Links:create'),
    ),
    'links_edit' => array(
        'path' => '/links/{linkID}/edit',
        'defaults' => array('_controller' => 'Admin:Links:edit'),
        'requirements' => array('linkID' => '\d+'),
    ),
    'links_update' => array(
        'path' => '/links/update',
        'defaults' => array('_controller' => 'Admin:Links:update'),
        'requirements' => array('_method' => 'POST'),
    ),
    'links_delete' => array(
        'path' => '/links/{linkID}/delete',
        'defaults' => array('_controller' => 'Admin:Links:delete'),
        'requirements' => array('linkID' => '\d+'),
    ),
    'links_search' => array(
        'path' => '/links/search',
        'defaults' => array('_controller' => 'Admin:Links:search'),
    ),
    'links_clear' => array(
        'path' => '/links/clear',
        'defaults' => array('_controller' => 'Admin:Links:clear'),
    ),
    'links_stats' => array(
        'path' => '/links/stats',
        'defaults' => array('_controller' => 'Admin:Links:stats'),
        'requirements' => array('_method' => 'POST'),
    ),
    'servers_index' => array(
        'path' => '/servers',
        'defaults' => array('_controller' => 'Admin:Servers:index'),
    ),
    'servers_create' => array(
        'path' => '/servers/create',
        'defaults' => array('_controller' => 'Admin:Servers:create'),
    ),
    'servers_edit' => array(
        'path' => '/servers/{serverID}/edit',
        'defaults' => array('_controller' => 'Admin:Servers:edit'),
        'requirements' => array('serverID' => '\d+'),
    ),
    'servers_update' => array(
        'path' => '/servers/update',
        'defaults' => array('_controller' => 'Admin:Servers:update'),
        'requirements' => array('_method' => 'POST'),
    ),
    'servers_hint' => array(
        'path' => '/servers/hint',
        'defaults' => array('_controller' => 'Admin:Servers:hint'),
        'requirements' => array('_method' => 'POST'),
    ),
    'servers_delete' => array(
        'path' => '/servers/{serverID}/delete',
        'defaults' => array('_controller' => 'Admin:Servers:delete'),
        'requirements' => array('serverID' => '\d+'),
    ),
    'submitted_index' => array(
        'path' => '/submitted',
        'defaults' => array('_controller' => 'Admin:Submitted:index'),
    ),
    'submitted_view' => array(
        'path' => '/submitted/{episodeID}/view',
        'defaults' => array('_controller' => 'Admin:Submitted:view'),
        'requirements' => array('episodeID' => '\d+'),
    ),
    'submitted_update' => array(
        'path' => '/submitted/update',
        'defaults' => array('_controller' => 'Admin:Submitted:update'),
        'requirements' => array('_method' => 'POST'),
    ),
    'submitted_delete' => array(
        'path' => '/submitted/{episodeID}/delete',
        'defaults' => array('_controller' => 'Admin:Submitted:delete'),
        'requirements' => array('episodeID' => '\d+'),
    ),
    'submitted_stats' => array(
        'path' => '/submitted/stats',
        'defaults' => array('_controller' => 'Admin:Submitted:stats'),
        'requirements' => array('_method' => 'POST'),
    ),
    'news_index' => array(
        'path' => '/news',
        'defaults' => array('_controller' => 'Admin:News:index'),
    ),
    'news_create' => array(
        'path' => '/news/create',
        'defaults' => array('_controller' => 'Admin:News:create'),
    ),
    'news_edit' => array(
        'path' => '/news/{newsID}/edit',
        'defaults' => array('_controller' => 'Admin:News:edit'),
        'requirements' => array('newsId' => '\d+'),
    ),
    'news_update' => array(
        'path' => '/news/update',
        'defaults' => array('_controller' => 'Admin:News:update'),
        'requirements' => array('_method' => 'POST'),
    ),
    'news_delete' => array(
        'path' => '/news/{newsID}/delete',
        'defaults' => array('_controller' => 'Admin:News:delete'),
        'requirements' => array('newsId' => '\d+'),
    ),
    'news_alias' => array(
        'path' => '/news/alias',
        'defaults' => array('_controller' => 'Admin:News:alias'),
        'requirements' => array('_method' => 'POST'),
    ),
    'tags_index' => array(
        'path' => '/tags',
        'defaults' => array('_controller' => 'Admin:Tags:index'),
    ),
    'tags_create' => array(
        'path' => '/tags/create',
        'defaults' => array('_controller' => 'Admin:Tags:create'),
    ),
    'tags_edit' => array(
        'path' => '/tags/{tagID}/edit',
        'defaults' => array('_controller' => 'Admin:Tags:edit'),
        'requirements' => array('tagID' => '\d+'),
    ),
    'tags_update' => array(
        'path' => '/tags/update',
        'defaults' => array('_controller' => 'Admin:Tags:update'),
        'requirements' => array('_method' => 'POST'),
    ),
    'tags_delete' => array(
        'path' => '/tags/{tagID}/delete',
        'defaults' => array('_controller' => 'Admin:Tags:delete'),
        'requirements' => array('tagID' => '\d+'),
    ),
    'faq_index' => array(
        'path' => '/faq',
        'defaults' => array('_controller' => 'Admin:Faq:index'),
    ),
    'faq_create' => array(
        'path' => '/faq/create',
        'defaults' => array('_controller' => 'Admin:Faq:create'),
    ),
    'faq_edit' => array(
        'path' => '/faq/{faqID}/edit',
        'defaults' => array('_controller' => 'Admin:Faq:edit'),
        'requirements' => array('faqID' => '\d+'),
    ),
    'faq_update' => array(
        'path' => '/faq/update',
        'defaults' => array('_controller' => 'Admin:Faq:update'),
        'requirements' => array('_method' => 'POST'),
    ),
    'faq_delete' => array(
        'path' => '/faq/{faqID}/delete',
        'defaults' => array('_controller' => 'Admin:Faq:delete'),
        'requirements' => array('faqID' => '\d+'),
    ),
    'pages_index' => array(
        'path' => '/pages',
        'defaults' => array('_controller' => 'Admin:Pages:index'),
    ),
    'pages_create' => array(
        'path' => '/pages/create',
        'defaults' => array('_controller' => 'Admin:Pages:create'),
    ),
    'pages_edit' => array(
        'path' => '/pages/{pageID}/edit',
        'defaults' => array('_controller' => 'Admin:Pages:edit'),
        'requirements' => array('pageID' => '\d+'),
    ),
    'pages_update' => array(
        'path' => '/pages/update',
        'defaults' => array('_controller' => 'Admin:Pages:update'),
        'requirements' => array('_method' => 'POST'),
    ),
    'pages_delete' => array(
        'path' => '/pages/{pageID}/delete',
        'defaults' => array('_controller' => 'Admin:Pages:delete'),
        'requirements' => array('pageID' => '\d+'),
    ),
    'pages_alias' => array(
        'path' => '/pages/alias',
        'defaults' => array('_controller' => 'Admin:Pages:alias'),
        'requirements' => array('_method' => 'POST'),
    ),
    'menu_index' => array(
        'path' => '/menu',
        'defaults' => array('_controller' => 'Admin:Menu:index'),
    ),
    'menu_create' => array(
        'path' => '/menu/create',
        'defaults' => array('_controller' => 'Admin:Menu:create'),
    ),
    'menu_edit' => array(
        'path' => '/menu/{menuID}/edit',
        'defaults' => array('_controller' => 'Admin:Menu:edit'),
        'requirements' => array('menuID' => '\d+'),
    ),
    'menu_update' => array(
        'path' => '/menu/update',
        'defaults' => array('_controller' => 'Admin:Menu:update'),
        'requirements' => array('_method' => 'POST'),
    ),
    'menu_delete' => array(
        'path' => '/menu/{menuID}/delete',
        'defaults' => array('_controller' => 'Admin:Menu:delete'),
        'requirements' => array('menuID' => '\d+'),
    ),
    'ads_index' => array(
        'path' => '/ads',
        'defaults' => array('_controller' => 'Admin:Ads:index'),
    ),
    'ads_create' => array(
        'path' => '/ads/create',
        'defaults' => array('_controller' => 'Admin:Ads:create'),
    ),
    'ads_edit' => array(
        'path' => '/ads/{adID}/edit',
        'defaults' => array('_controller' => 'Admin:Ads:edit'),
        'requirements' => array('adID' => '\d+'),
    ),
    'ads_update' => array(
        'path' => '/ads/update',
        'defaults' => array('_controller' => 'Admin:Ads:update'),
        'requirements' => array('_method' => 'POST'),
    ),
    'ads_delete' => array(
        'path' => '/ads/{adID}/delete',
        'defaults' => array('_controller' => 'Admin:Ads:delete'),
        'requirements' => array('adID' => '\d+'),
    ),
    'users_index' => array(
        'path' => '/users',
        'defaults' => array('_controller' => 'Admin:Users:index'),
    ),
    'users_create' => array(
        'path' => '/users/create',
        'defaults' => array('_controller' => 'Admin:Users:create'),
    ),
    'users_edit' => array(
        'path' => '/users/{userID}/edit',
        'defaults' => array('_controller' => 'Admin:Users:edit'),
        'requirements' => array('userID' => '\d+'),
    ),
    'users_change' => array(
        'path' => '/users/{userID}/change/{action}/{value}',
        'defaults' => array('_controller' => 'Admin:Users:change'),
        'requirements' => array('userID' => '\d+', 'action' => 'enabled|role', 'value' => '\w+'),
    ),
    'users_update' => array(
        'path' => '/users/update',
        'defaults' => array('_controller' => 'Admin:Users:update'),
        'requirements' => array('_method' => 'POST'),
    ),
    'users_delete' => array(
        'path' => '/users/{userID}/delete',
        'defaults' => array('_controller' => 'Admin:Users:delete'),
    ),
    'users_search' => array(
        'path' => '/users/search',
        'defaults' => array('_controller' => 'Admin:Users:search'),
    ),
    'users_check' => array(
        'path' => '/users/check',
        'defaults' => array('_controller' => 'Admin:Users:check'),
        'requirements' => array('_method' => 'POST'),
    ),
    'users_stats' => array(
        'path' => '/users/stats',
        'defaults' => array('_controller' => 'Admin:Users:stats'),
        'requirements' => array('_method' => 'POST'),
    ),
    'sessions_index' => array(
        'path' => '/sessions',
        'defaults' => array('_controller' => 'Admin:UsersOnline:index'),
    ),
    'sessions_view' => array(
        'path' => '/sessions/{sessID}/view',
        'defaults' => array('_controller' => 'Admin:UsersOnline:view'),
        'requirements' => array('sessID' => '\w+'),
    ),
    'sessions_update' => array(
        'path' => '/sessions/update',
        'defaults' => array('_controller' => 'Admin:UsersOnline:update'),
        'requirements' => array('_method' => 'POST'),
    ),
    'sessions_delete' => array(
        'path' => '/sessions/{sessID}/delete',
        'defaults' => array('_controller' => 'Admin:UsersOnline:delete'),
        'requirements' => array('sessID' => '\w+'),
    ),
    'sessions_search' => array(
        'path' => '/sessions/search',
        'defaults' => array('_controller' => 'Admin:UsersOnline:search'),
    ),
    'comments_index' => array(
        'path' => '/comments',
        'defaults' => array('_controller' => 'Admin:Comments:index'),
    ),
    'comments_edit' => array(
        'path' => '/comments/{commentID}/edit',
        'defaults' => array('_controller' => 'Admin:Comments:edit'),
        'requirements' => array('commentID' => '\d+'),
    ),
    'comments_update' => array(
        'path' => '/comments/update',
        'defaults' => array('_controller' => 'Admin:Comments:update'),
        'requirements' => array('_method' => 'POST'),
    ),
    'comments_delete' => array(
        'path' => '/comments/{commentID}/delete',
        'defaults' => array('_controller' => 'Admin:Comments:delete'),
        'requirements' => array('commentID' => '\d+'),
    ),
    'comments_search' => array(
        'path' => '/comments/search',
        'defaults' => array('_controller' => 'Admin:Comments:search'),
    ),
    'reports_index' => array(
        'path' => '/reports',
        'defaults' => array('_controller' => 'Admin:Reports:index'),
    ),
    'reports_view' => array(
        'path' => '/reports/{reportID}/view',
        'defaults' => array('_controller' => 'Admin:Reports:view'),
        'requirements' => array('reportID' => '\d+'),
    ),
    'reports_reply' => array(
        'path' => '/reports/{reportID}/reply',
        'defaults' => array('_controller' => 'Admin:Reports:reply'),
        'requirements' => array('_method' => 'POST', 'reportID' => '\d+'),
    ),
    'reports_update' => array(
        'path' => '/reports/update',
        'defaults' => array('_controller' => 'Admin:Reports:update'),
        'requirements' => array('_method' => 'POST'),
    ),
    'reports_delete' => array(
        'path' => '/reports/{reportID}/delete',
        'defaults' => array('_controller' => 'Admin:Reports:delete'),
        'requirements' => array('reportID' => '\d+'),
    ),
    'reports_search' => array(
        'path' => '/reports/search',
        'defaults' => array('_controller' => 'Admin:Reports:search'),
    ),
    'backup_index' => array(
        'path' => '/backup',
        'defaults' => array('_controller' => 'Admin:Backup:index'),
    ),
    'backup_download' => array(
        'path' => '/backup/{fileName}/download',
        'defaults' => array('_controller' => 'Admin:Backup:download'),
        'requirements' => array('fileName' => '[\w\.]+'),
    ),
    'backup_delete' => array(
        'path' => '/backup/{fileName}/delete',
        'defaults' => array('_controller' => 'Admin:Backup:delete'),
        'requirements' => array('fileName' => '[\w\.]+'),
    ),
);