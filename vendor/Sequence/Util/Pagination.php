<?php


namespace Sequence\Util;


/**
 * A pagination compatible with bootstrap style
 */
class Pagination
{
    /** @var int  */
    protected $currentPage;

    /** @var int  */
    protected $perPage;

    /** @var int  */
    protected $totalCount;

    /** @var int  */
    protected $range;

    /** @var string  */
    protected $urlNeedle;

    /** @var  string */
    protected $baseUrl;

    /** @var  string */
    protected $url;

    /** @var string */
    protected $navBack;

    /** @var string  */
    protected $navNext;

    /** @var  string */
    protected $html;

    public function __construct()
    {
        $this->currentPage  = 1;
        $this->perPage      = 40;
        $this->totalCount   = 0;
        $this->range        = 3;
        $this->urlNeedle    = '#PAGE#';
        $this->url          = '?page='.$this->urlNeedle;
        $this->baseUrl      = $_SERVER['SCRIPT_NAME'];
        $this->navBack      = '&laquo;';
        $this->navNext      = '&raquo;';
    }

    /**
     * @param $val
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setCurrentPage($val)
    {
        $val = (int) $val;

        if($val === 0 || $val > $this->totalPages())
        {
            throw new \InvalidArgumentException(sprintf('Value "%s" is greater than $totalCount, or its equal zero.', $val));
        }

        $this->currentPage = $val;

        return $this;
    }

    /**
     * @param $val
     * @return $this
     */
    public function setPerPage($val)
    {
        $this->perPage = $val;

        return $this;
    }

    /**
     * @param $val
     * @return $this
     */
    public function setTotalCount($val)
    {
        $this->totalCount = $val;

        return $this;
    }

    /**
     * @param $val
     * @return $this
     */
    public function setRange($val)
    {
        $this->range = $val;

        return $this;
    }

    /**
     * @param $val
     * @return $this
     */
    public function setBaseUrl($val)
    {
        $this->baseUrl = $val;

        return $this;
    }

    /**
     * Sets the link with pagination, for example:
     * * - /custompage/page/#PAGE#
     * * - custompage.php?page=#PAGE#
     *
     * @param    string $url
     * @return   Pagination
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @param string $needle
     * @return $this
     */
    public function setUrlNeedle($needle)
    {
        $this->urlNeedle = $needle;

        return $this;
    }

    /**
     * Sets name of previous button. It can be text or html.
     * For default is "&laquo;".
     *
     * @param    mixed $val
     * @return   Pagination
     */
    public function navBack($val)
    {
        $this->navBack = $val;

        return $this;
    }

    /**
     * Sets name of next button. It can be text or html.
     * For default is "&raquo;".
     *
     * @param    mixed $val
     * @return   Pagination
     */
    public function navNext($val)
    {
        $this->navNext = $val;

        return $this;
    }

    /**
     * @return int
     */
    public function offset()
    {
        if($this->currentPage > 1)
        {
            return $this->currentPage * $this->perPage - $this->perPage;
        } else
        {
            return 0;
        }
    }

    /**
     * @return int
     */
    public function limit()
    {
        return $this->perPage;
    }

    /**
     * @return int
     */
    public function indexStart()
    {
        if($this->currentPage > 1)
        {
            $result = $this->perPage * ($this->currentPage - 1);
        } else
        {
            $result = 0;
        }

        return $result;
    }

    /**
     * @return int
     */
    public function indexEnd()
    {
        if($this->totalCount > $this->perPage)
        {
            $val = $this->indexStart() + ($this->perPage - 1);
            if($val > $this->totalCount)
            {
                $result = $this->totalCount - 1;
            } else
            {
                $result = $val;
            }
        } else
        {
            $result = $this->totalCount - 1;
        }

        return $result;
    }

    /**
     * @param null|string $style local style for pagination block like "float:left;"
     * @param null|string $class
     *
     * @return null|string
     */
    public function getHtml($style = null, $class = null)
    {
        if(null !== $this->html)
        {
            return $this->html;
        }

        if($this->totalCount <= $this->perPage)
        {
            return null;
        }

        $res = '';

        if($this->currentPage >= 2)
        {
            $res .= '<li><a href="'.$this->createLink($this->previousPage()).'">'.$this->navBack.'</a></li>';
        } else
        {
            $res .= '<li class="disabled"><span>'.$this->navBack.'</span></li>';
        }

        if($this->currentPage > ($this->range + 1))
        {
            if(1 === $this->currentPage)
            {
                $res .= '<li class="active"><span>1</span></li>';
            } else
            {
                $res .= '<li><a href="'.$this->createLink(1).'">1</a></li>';
            }

            if($this->currentPage > ($this->range + 2))
            {
                $res .= '<li class="disabled"><span>&hellip;</span></li>';
            }
        }

        $idxFst = max($this->currentPage - $this->range, 1);
        $idxLst = min($this->currentPage + $this->range, $this->totalPages());

        for($i = $idxFst; $i <= $idxLst; $i++)
        {
            if($i == $this->currentPage)
            {
                $res .= '<li class="active"><span>'.$i.'</span></li>';
            } else
            {
                $res .= '<li><a href="'.$this->createLink($i).'">'.$i.'</a></li>';
            }
        }

        if($this->currentPage < ($this->totalPages() - $this->range))
        {
            if($this->currentPage < ($this->totalPages() - ($this->range + 1)))
            {
                $res .= '<li class="disabled"><span>&hellip;</span></li>';
            }

            $res .= '<li><a href="'.$this->createLink($this->totalPages()).'">'.$this->totalPages().'</a></li>';
        }

        if($this->nextPage() <= $this->totalPages())
        {
            $res .= '<li><a href="'.$this->createLink($this->nextPage()).'">'.$this->navNext.'</a></li>';
        } else
        {
            $res .= '<li class="disabled"><span>'.$this->navNext.'</span></li>';
        }

        $this->html = '<ul class="pagination'.($class ? ' '.$class : null).'"'.(!$style ? null : 'style="'.$style.'"').'>'.$res.'</ul>';

        return $this->html;
    }

    /**
     * @return float|int
     */
    protected function totalPages()
    {
        return $this->totalCount >= $this->perPage ? ceil($this->totalCount/$this->perPage) : 1;
    }


    /**
     * @return int
     */
    protected function previousPage()
    {
        return $this->currentPage-1;
    }


    /**
     * @return int
     */
    protected function nextPage()
    {
        return $this->currentPage+1;
    }


    /**
     * @param $page
     * @return mixed
     */
    protected function createLink($page)
    {
        if(1 == $page)
        {
            return $this->baseUrl;
        } else
        {
            return str_replace($this->urlNeedle, $page, $this->url);
        }
    }
}