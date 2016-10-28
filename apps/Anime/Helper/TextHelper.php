<?php


namespace Anime\Helper;


use Symfony\Component\Templating\Helper\AssetsHelper;
use Symfony\Component\Templating\Helper\Helper;

class TextHelper extends Helper
{
    /**
     *
     *
     * @param $value
     * @param int $length
     * @param string $separator
     *
     * @return string
     */
    public function truncate($value, $length = 100, $separator = '&hellip;')
    {
        if(mb_strlen($value, $this->getCharset()) > $length)
        {
            return rtrim(mb_substr($value, 0, $length, $this->getCharset())).$separator;
        }

        return $value;
    }

    /**
     * @param string|\DateTime $datetime
     * @param bool $full
     *
     * @return string
     */
    public function timeElapsed($datetime, $full = false)
    {
        $now = new \DateTime;
        $ago = $datetime instanceof \DateTime ? $datetime : new \DateTime($datetime);
        $diff = $now->diff($ago);

        $months = array('stycznia', 'lutego', 'marca', 'kwietnia', 'maja', 'czerwca', 'lipca', 'sierpnia', 'września', 'października', 'listopada', 'grudnia');

        $hours = $diff->h;
        $hours += 24 * $diff->days;

        if(true === $full)
        {
            return $ago->format('j').' '.$months[$ago->format('n')-1].' '.$ago->format('Y').' o '.$ago->format('H:i');
        } elseif(!strcmp($full, 'date'))
        {
            return $ago->format('j').' '.$months[$ago->format('n')-1].' '.$ago->format('Y');
        } elseif($diff->s <= 60 && $hours == 0 && $diff->i == 0)
        {
            return 'kilka sekund temu';
        } elseif($diff->i <= 60 && $hours == 0)
        {
            return $diff->i == 1 ? 'około minuty temu' : $diff->i.' minut(y) temu';
        } elseif($hours <= 24)
        {
            return $hours.' godz.';
        } elseif($hours > 24 && $hours <= 48)
        {
            return 'Wczoraj o '.$ago->format('H:i');
        } else
        {
            return $ago->format('j').' '.$months[$ago->format('n')-1].($ago->format('Y') !== $now->format('Y') ? ' '.$ago->format('Y') : '').' o '.$ago->format('H:i');
        }
    }

    /**
     * @param $string
     *
     * @return string
     */
    public function nl2p($string)
    {
        $paragraphs = '';

        foreach(explode("\n", $string) as $line)
        {
            if(trim($line))
            {
                $paragraphs .= '<p>'.$line.'</p>';
            }
        }

        return $paragraphs;
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public function bbcode($text)
    {
        $patterns = array(
            '#\[b\](.*?)\[/b\]#si',
            '#\[i\](.*?)\[/i\]#si',
            '#\[u\](.*?)\[/u\]#si',
            '#\[admin\](.*?)\[/admin\]#si',
            '#\[ukryj\](.*?)\[/ukryj]#si',
            '#\[spoiler\](.*?)\[/spoiler]#si',
            '#\[cytat\](.*?)\[/cytat]#si',
            '#\[quote\](.*?)\[/quote]#si',
        );

        $spoiler = '<div class="bs-callout bs-callout-info spoiler"><h3>Spoiler <button class="btn btn-sm btn-default">pokaż</button></h3><p class="collapse">\1</p></div>';
        $quote = '<div class="bs-callout bs-callout-info spoiler"><p>\1</p></div>';

        $replacements = array(
            '<strong>\1</strong>',
            '<em>\1</em>',
            '<ins>\1</ins>',
            '<span class="text-danger">\1</span>',
            $spoiler,
            $spoiler,
            $quote,
            $quote
        );

        $text = nl2br(trim($text));
        $text = preg_replace($patterns, $replacements, $text);

        return $text;
    }

    public function avatar($avatar, AssetsHelper $assets)
    {
        if(strstr($avatar, 'http'))
        {
            return $avatar;
        } else
        {
            return $assets->getUrl('avatars/'.(empty($avatar) ? 'brak.png' : $avatar));
        }
    }

    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     *
     * @api
     */
    public function getName()
    {
        return 'text';
    }
} 