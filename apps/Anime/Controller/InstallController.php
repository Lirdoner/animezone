<?php


namespace Anime\Controller;


use Anime\Model\Category\Category;
use Anime\Model\Comment\Comment;
use Anime\Model\Link\Link;
use Anime\Model\Report\Report;
use Anime\Model\Watch\Watch;
use Sequence\Database\Database;
use Sequence\Controller;

class InstallController extends Controller
{
    public function indexAction()
    {
        set_time_limit(0);
        /** @var \Sequence\Database\Database $oldDb */
        $oldDb = $this->get('database_manager')->getConnection('az_local2');
        $db = $this->getDatabase();

        $response = array();

        //categories
        //$response['categories'] = $this->categories($db, $oldDb);

        //species
        //$response['species'] = $this->species($db, $oldDb);
        //$response['species_for_category'] = $this->speciesForCategory($db, $oldDb);

        //topics
        //$response['topics'] = $this->topics($db, $oldDb);
        //$response['topics_for_category'] = $this->topicsForCategory($db, $oldDb);

        //type
        //$response['type'] = $this->type($db, $oldDb);
        //$response['type_for_category'] = $this->typeForCategory($db, $oldDb);

        //episodes
        //$response['episodes'] = $this->episodes($db, $oldDb);

        //links
        //$response['links'] = $this->links($db, $oldDb);

        //users
        //$response['users'] = $this->users($db, $oldDb);

        //comments
        //$response['comments'] = $this->comments($db, $oldDb);

        //favorites
        //$response['favorites'] = $this->favorites($db, $oldDb);

        //news
        //$response['news'] = $this->news($db, $oldDb);

        //ratings
        //$response['ratings'] = $this->ratings($db, $oldDb);

        //reports
        //$response['reports'] = $this->reports($db, $oldDb);

        //servers
        //$response['servers'] = $this->servers($db, $oldDb);

        //submitted episodes
        //$response['submitted_episodes'] = $this->submitted($db, $oldDb);

        //watched episodes
        //$response['watched_episodes'] = $this->watched($db, $oldDb);

        var_dump($response);
        exit;
    }

    protected function categories(Database $db, Database $oldDb)
    {
        $data = $oldDb->select()->from('az_kategorie')->get();

        $response = 0;

        foreach($data->fetchAll() as $row)
        {
            $release = null;

            if(!strcmp('film', $row['rodzaj']))
            {
                $release = Category::RELEASE_MOVIE;
            } elseif(!strcmp('TV', $row['rodzaj']))
            {
                $release = Category::RELEASE_TV;
            } elseif(!strcmp('OVA', $row['rodzaj']))
            {
                $release = Category::RELEASE_OVA;
            }

            if(!strcmp('Emitowane', $row['status']))
            {
                $status = Category::STATUS_EMITTED;
            } elseif(!strcmp('Zakończone', $row['status']))
            {
                $status = Category::STATUS_ENDED;
            } elseif(!strcmp('Nadchodzące', $row['status']))
            {
                $status = Category::STATUS_COMING;
            } else
            {
                $status = Category::STATUS_RECENTLY_ENDED;
            }

            $rating = $oldDb->select('AVG(ocena_wartosc) as avg')->
                from('az_oceny')->where(array('ocena_kategoria' => $row['id_kategorii']))->
                get()->
                fetch();

            $response += $db->insert('categories', array(
                'id' => $row['id_kategorii'],
                'letter' => $row['litera'],
                'name' => stripcslashes($row['nazwa']),
                'alias' => $row['alias'],
                'image' => $row['obrazek'],
                'description' => stripcslashes($row['opis']),
                'alternate' => stripcslashes($row['alternatywny']),
                'pegi' => $row['wiek'],
                'year' => $row['rok'],
                'season' => 0,
                'status' => $status,
                'release' => $release,
                'views' => $row['wyswietlenia'],
                'fans' => 0,
                'rating_avg' => round($rating['avg'], 2),
                'rating_count' => 0,
                'watching' => 0,
                'watched' => 0,
                'plans' => 0,
                'stopped' => 0,
                'abandoned' => 0,
				'trailer' => stripcslashes($row['trailer']),
            ))->get();

            unset($row, $watching, $rating, $fans, $release, $species, $topics, $types);
        }

        return $response;
    }

    protected function users(Database $db, Database $oldDb)
    {
        $data = $oldDb->select()->from('az_users')->order('user_id ASC')->limit(5000)->offset(15000)->get();

        $response = array();

        foreach($data->fetchAll() as $row)
        {
            $password = hash('sha256', uniqid());

            //basic data
            $response[$row['user_id']]['basic_data'] += $db->insert('users', array(
                'id' => $row['user_id'],
                'name' => $row['user_login'],
                'password' => $password,
                'email' => $row['user_email'],
                'enabled' => ($row['user_blocked'] == 1 ? 2 : 1),
                'role' => ($row['user_level'] ? 'ROLE_ADMIN' : 'ROLE_USER'),
                'ip' => $row['user_ip'],
                'date_created' => date_format(date_create('@'.$row['user_joined']), 'Y-m-d H:i:s'),
                'last_login' => date_format(date_create('@'.$row['user_lastvisit']), 'Y-m-d H:i:s'),
            ))->get();

            //custom data
            $favorites = $oldDb->select('count(ulubione_id) as value')->from('az_ulubione')->where(array('ulubione_uzytkownik' => $row['user_id']))->get()->fetch();
            $rated = $oldDb->select('count(ocena_id) as value')->from('az_oceny')->where(array('ocena_uzytkownik' => $row['user_id']))->get()->fetch();
            $commented = $oldDb->select('count(komentarz_id) as value')->from('az_komentarze')->where(array('komentarz_kto' => $row['user_id']))->get()->fetch();
            $watched = $oldDb->select('count(ogladam_id) as value')->from('az_ogladam')->where(array('ogladam_uzytkownik' => $row['user_id']))->get()->fetch();

            $response[$row['user_id']]['custom_data'] += $db->insert('users_custom_field', array(
                'user_id' => $row['user_id'],
                'location' => $row['user_login'],
                'birthdate' => $row['user_birthdate'],
                'gender' => ($row['user_gender'] == 'Kobieta' ? 2 : 1),
                'favorites' => $favorites['value'],
                'rated' => $rated['value'],
                'commented' => $commented['value'],
                'watching' => $watched['value'],
                'watched' => 0,
                'plans' => 0,
                'stopped' => 0,
                'abandoned' => 0,
            ))->get();
        }

        return $response;
    }

    protected function episodes(Database $db, Database $oldDb)
    {
        $data = $oldDb->select()->from('az_odcinki')->limit(5000)->offset(15000)->get();

        $response = 0;

        foreach($data->fetchAll() as $row)
        {
            $response += $db->insert('episodes', array(
                'id' => $row['id_odcinka'],
                'category_id' => $row['kategoria'],
                'number' => $row['numer'],
                'title' => $row['tytul'],
                'status' => $row['status'],
                'filler' => ('Tak' == $row['filler'] ? 1 : 0),
                'date_add' => date_format(date_create('@'.$row['data_dodania']), 'Y-m-d H:i:s'),
            ))->get();
        }

        return $response;
    }

    protected function comments(Database $db, Database $oldDb)
    {
        $data = $oldDb->select()->from('az_komentarze')->limit(5000)->offset(15000)->get();

        $response = array();

        foreach($data->fetchAll() as $row)
        {
            $response[$row['komentarz_id']] = $db->insert('comments', array(
                'date' => date_format(date_create('@'.$row['komentarz_data']), 'Y-m-d H:i:s'),
                'message' => stripcslashes($row['komentarz_tresc']),
                'id' => $row['komentarz_id'],
                'to' => $row['komentarz_do'],
                'user_id' => $row['komentarz_kto'],
                'type' => Comment::TYPE_CATEGORY,
            ))->get();
        }

        return $response;
    }

    protected function favorites(Database $db, Database $oldDb)
    {
        $data = $oldDb->select()->from('az_ulubione')->limit(30000)->offset(110000)->get();

        $response = array();

        foreach($data->fetchAll() as $row)
        {
            $response[$row['ulubione_id']] = $db->insert('favorites', array(
                'id' => $row['ulubione_id'],
                'user_id' => $row['ulubione_uzytkownik'],
                'category_id' => $row['ulubione_kategoria'],
                'date' => date_format(date_create('@'.$row['ulubione_data']), 'Y-m-d H:i:s'),
            ))->get();
        }

        return $response;
    }

    protected function links(Database $db, Database $oldDb)
    {
        $data = $oldDb->select()->from('az_linki')->limit(10000)->offset(0)->get();

        $response = 0;

        foreach($data->fetchAll() as $row)
        {
            if('pl' == $row['wersja'])
            {
                $lang = Link::LANG_PL;
                $lang_id = Link::LANG_ID_PL;
            } elseif('eng' == $row['wersja'])
            {
                $lang = Link::LANG_EN;
                $lang_id = Link::LANG_ID_EN;
            } elseif('jap' == $row['wersja'])
            {
                $lang = Link::LANG_JP;
                $lang_id = Link::LANG_ID_JP;
            } else
            {
                $lang = Link::LANG_PL;
                $lang_id = Link::LANG_ID_PL;
            }

            //fix width and height for some servers
            $row['kod'] = stripcslashes($row['kod']);

            if(31 == $row['serwer'] || 7 == $row['serwer'] || 27 == $row['serwer'])
            {
                $row['kod'] = preg_replace(array(
                    '/500/', '/647/'
                ), array(
                    '425', '711'
                ), $row['kod']);
            } elseif(15 == $row['serwer'] || 37 == $row['serwer'])
            {
                $row['kod'] = preg_replace(array(
                    '/h=500/', '/w=647/', '/height=500/', '/width=647/',
                ), array(
                    'h=100%', 'w=100%', 'height=100%', 'width=100%',
                ), $row['kod']);
            } elseif(8 == $row['serwer'])
            {
                $row['kod'] = preg_replace(array(
                    '/500px/', '/647px/', '/height=500/', '/width=647/',
                ), array(
                    '100%', '100%', 'height=auto', 'width=auto',
                ), $row['kod']);
            } elseif(17 == $row['serwer'])
            {
                $row['kod'] = preg_replace(array(
                    '/\?w=647\&h=500/', '/\?w=647\&amp;h=500/'
                ), array(
                    '', ''
                ), $row['kod']);
            }

            $row['kod'] = preg_replace(array(
                '/height="500"/', '/width="647"/',
            ), array(
                '', ''
            ), $row['kod']);

            $response += $db->insert('links', array(
                'id' => $row['id_linku'],
                'episode_id' => $row['do_odcinka'],
                'server_id' => $row['serwer'],
                'lang' => $lang,
                'lang_id' => $lang_id,
                'code' => $row['kod'],
                'info' => stripcslashes($row['dopisek']),
            ))->get();
        }

        return $response;
    }

    protected function news(Database $db, Database $oldDb)
    {
        $data = $oldDb->select()->from('az_nowosci')->get();

        $response = array();

        foreach($data->fetchAll() as $row)
        {
            $response[$row['news_id']] = $db->insert('news', array(
                'id' => $row['news_id'],
                'title' => stripcslashes($row['news_title']),
                'alias' => $row['news_alias'],
                'description' => stripcslashes($row['news_content']),
                'date' => $row['news_date'],
            ))->get();
        }

        return $response;
    }

    protected function ratings(Database $db, Database $oldDb)
    {
        $data = $oldDb->select()->from('az_oceny')->limit(10000)->offset(160000)->get();

        $response = array();

        foreach($data->fetchAll() as $row)
        {
            $response[$row['ocena_id']] = $db->insert('rating', array(
                'id' => $row['ocena_id'],
                'user_id' => $row['ocena_uzytkownik'],
                'category_id' => $row['ocena_kategoria'],
                'value' => $row['ocena_wartosc'],
                'date' => date_format(date_create('@'.$row['ocena_data']), 'Y-m-d H:i:s'),
            ))->get();
        }

        return $response;
    }

    protected function reports(Database $db, Database $oldDb)
    {
        $data = $oldDb->select()->from('az_reporty')->get();

        $response = array();

        foreach($data->fetchAll() as $row)
        {
            $type = null;

            if('Link' == $row['report_type'])
            {
                $type = Report::TYPE_LINK;
            } elseif('Komentarz' == $row['report_type'])
            {
                $type = Report::TYPE_COMMENT;
            } elseif('Kontakt' == $row['report_type'])
            {
                $type = Report::TYPE_CONTACT;
            }

            $response[$row['report_id']] = $db->insert('reports', array(
                'id' => $row['report_id'],
                'type' => $type,
                'content' => stripcslashes($row['report_content']),
                'mail' => stripcslashes($row['report_mail']),
                'subject' => stripcslashes($row['report_subject']),
                'link_id' => $row['link_id'],
                'report_ip' => $row['report_ip'],
            ))->get();
        }

        return $response;
    }

    protected function servers(Database $db, Database $oldDb)
    {
        $data = $oldDb->select()->from('az_serwery')->get();

        $response = array();

        foreach($data->fetchAll() as $row)
        {
            $response[$row['id_serwer']] = $db->insert('servers', array(
                'id' => $row['id_serwer'],
                'name' => $row['nazwa_serwer'],
                'type' => $row['typ_serwer'],
            ))->get();
        }

        return $response;
    }

    protected function submitted(Database $db, Database $oldDb)
    {
        $data = $oldDb->select()->from('az_poczekalnia')->get();

        $response = array();

        foreach($data->fetchAll() as $row)
        {
            $response[$row['id']] = $db->insert('submitted_episode', array(
                'id' => $row['id'],
                'title' => stripcslashes($row['tytul']),
                'links' => stripcslashes($row['linki']),
                'ip' => $row['ip'],
            ))->get();
        }

        return $response;
    }

    protected function species(Database $db, Database $oldDb)
    {
        $data = $oldDb->select()->from('az_gatunki')->get();

        $response = 0;

        foreach($data->fetchAll() as $row)
        {
            $response += $db->insert('species', array(
                'id' => $row['id_gatunku'],
                'name' => $row['nazwa_gatunku'],
            ))->get();
        }

        return $response;
    }

    protected function speciesForCategory(Database $db, Database $oldDb)
    {
        $data = $oldDb->select()->from('az_gatunki_has_kategorie')->get();

        $response = 0;

        foreach($data->fetchAll() as $row)
        {
            $response += $db->insert('species_for_category', array(
                'id' => $row['id_gat_kat'],
                'category_id' => $row['kategoria_gat'],
                'species_id' => $row['gatunek_kat'],
            ))->get();
        }

        return $response;
    }

    protected function topics(Database $db, Database $oldDb)
    {
        $data = $oldDb->select()->from('az_tematyka')->get();

        $response = 0;

        foreach($data->fetchAll() as $row)
        {
            $response += $db->insert('topics', array(
                'id' => $row['id_tematyka'],
                'name' => $row['nazwa_tematyka'],
            ))->get();
        }

        return $response;
    }

    protected function topicsForCategory(Database $db, Database $oldDb)
    {
        $data = $oldDb->select()->from('az_tematyka_has_kategorie')->get();

        $response = 0;

        foreach($data->fetchAll() as $row)
        {
            $response += $db->insert('topics_for_category', array(
                'id' => $row['id_tem_kat'],
                'category_id' => $row['kategoria_tem'],
                'topics_id' => $row['tematyka_kat'],
            ))->get();
        }

        return $response;
    }

    protected function type(Database $db, Database $oldDb)
    {
        $data = $oldDb->select()->from('az_typ')->get();

        $response = 0;

        foreach($data->fetchAll() as $row)
        {
            $response += $db->insert('type', array(
                'id' => $row['id_typ'],
                'name' => $row['nazwa_typ'],
            ))->get();
        }

        return $response;
    }

    protected function typeForCategory(Database $db, Database $oldDb)
    {
        $data = $oldDb->select()->from('az_typ_has_kategorie')->get();

        $response = 0;

        foreach($data->fetchAll() as $row)
        {
            $response += $db->insert('type_for_category', array(
                'id' => $row['id_typ_kat'],
                'category_id' => $row['kategoria_typ'],
                'type_id' => $row['typ_kat'],
            ))->get();
        }

        return $response;
    }

    protected function watched(Database $db, Database $oldDb)
    {
        $data = $oldDb->select()->from('az_ogladam')->limit(30000)->offset(90000)->get();

        $response = array();

        foreach($data->fetchAll() as $row)
        {
            $response[$row['ogladam_id']] = $db->insert('watching', array(
                'id' => $row['ogladam_id'],
                'user_id' => $row['ogladam_uzytkownik'],
                'category_id' => $row['ogladam_kategoria'],
                'type' => Watch::WATCHING,
                'date' => date_format(date_create('@'.$row['ogladam_data']), 'Y-m-d H:i:s'),
            ))->get();
        }

        return $response;
    }
} 