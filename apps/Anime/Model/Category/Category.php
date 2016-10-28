<?php


namespace Anime\Model\Category;


use Sequence\Database\Entity\BaseEntity;

class Category extends BaseEntity
{
    const RELEASE_MOVIE = 1;
    const RELEASE_OVA = 2;
    const RELEASE_TV = 3;
    const STATUS_EMITTED = 0;
    const STATUS_ENDED = 1;
    const STATUS_COMING = 2;
    const STATUS_RECENTLY_ENDED = 3;
    const SEASON_SPRING = 1;
    const SEASON_SUMMER = 2;
    const SEASON_AUTUMN = 3;
    const SEASON_WINTER = 4;

    /** @var  int */
    protected $id;

    /** @var  string */
    protected $letter;

    /** @var  string */
    protected $name;

    /** @var  string */
    protected $alias;

    /** @var  string */
    protected $image;

    /** @var  string */
    protected $description;

    /** @var  string */
    protected $alternate;

    /** @var  string */
    protected $pegi;

    /** @var  string */
    protected $year;

    /** @var  int */
    protected $season;

    /** @var array  */
    protected $seasonType = array(
        self::SEASON_SPRING => 'Zima',
        self::SEASON_SUMMER => 'Wiosna',
        self::SEASON_AUTUMN => 'Lato',
        self::SEASON_WINTER => 'Jesień',
    );

    /** @var  int */
    protected $status;

    /** @var array  */
    protected $statusType = array(
        self::STATUS_EMITTED => 'Emitowane',
        self::STATUS_ENDED => 'Zakończone',
        self::STATUS_COMING => 'Nadchodzące',
        self::STATUS_RECENTLY_ENDED => 'Niedawno zakończone',
    );

    /** @var  int */
    protected $release;

    /** @var array  */
    protected $releaseType = array(
        self::RELEASE_MOVIE => 'Film',
        self::RELEASE_OVA => 'OVA',
        self::RELEASE_TV => 'TV',
    );

    /** @var  int */
    protected $series;

    /** @var  int */
    protected $views = 0;

    /** @var  int */
    protected $fans = 0;

    /** @var  float */
    protected $rating_avg = 0;

    /** @var  int */
    protected $rating_count = 0;

    /** @var  int */
    protected $watching = 0;

    /** @var  int */
    protected $watched = 0;

    /** @var  int */
    protected $plans = 0;

    /** @var  int */
    protected $stopped = 0;

    /** @var  int */
    protected $abandoned = 0;

	/** @var  string */
    protected $trailer;

    protected $guarded = array('statusType', 'releaseType', 'seasonType');

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $letter
     */
    public function setLetter($letter)
    {
        $this->letter = $letter;
    }

    /**
     * @return string
     */
    public function getLetter()
    {
        return $this->letter;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param string $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $alternate
     */
    public function setAlternate($alternate)
    {
        $this->alternate = $alternate;
    }

    /**
     * @return string
     */
    public function getAlternate()
    {
        return $this->alternate;
    }

    /**
     * @param string $pegi
     */
    public function setPegi($pegi)
    {
        $this->pegi = $pegi;
    }

    /**
     * @return string
     */
    public function getPegi()
    {
        return $this->pegi;
    }

    /**
     * @param \DateTime $year
     */
    public function setYear(\DateTime $year)
    {
        $this->year = $year->format('Y');
    }

    /**
     * @return string
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param int $season
     */
    public function setSeason($season)
    {
        $this->season = $season;
    }

    /**
     * @param bool $name
     *
     * @return int
     */
    public function getSeason($name = false)
    {
        return $name ? $this->seasonType[$this->season] : $this->season;
    }

    /**
     * @return array
     */
    public function getSeasonType()
    {
        return $this->seasonType;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @param bool $name
     *
     * @return int
     */
    public function getStatus($name = false)
    {
        return $name ? $this->statusType[$this->status] : $this->status;
    }

    /**
     * @return array
     */
    public function getStatusType()
    {
        return $this->statusType;
    }

    /**
     * @param int $type
     */
    public function setRelease($type)
    {
        $this->release = $type;
    }

    /**
     * @param bool $name
     *
     * @return int
     */
    public function getRelease($name = false)
    {
        return $name ? $this->releaseType[$this->release] : $this->release;
    }

    /**
     * @return array
     */
    public function getReleaseType()
    {
        return $this->releaseType;
    }

    /**
     * @param int $series
     */
    public function setSeries($series)
    {
        $this->series = $series;
    }

    /**
     * @return int
     */
    public function getSeries()
    {
        return $this->series;
    }

    /**
     * @param int $views
     */
    public function setViews($views)
    {
        $this->views = $views;
    }

    /**
     * @return int
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * @param int $fans
     */
    public function setFans($fans)
    {
        $this->fans = $fans;
    }

    /**
     * @return int
     */
    public function getFans()
    {
        return $this->fans;
    }

    /**
     * @param float $rating_avg
     */
    public function setRatingAvg($rating_avg)
    {
        $this->rating_avg = $rating_avg;
    }

    /**
     * @return float
     */
    public function getRatingAvg()
    {
        return $this->rating_avg;
    }

    /**
     * @param int $rating_count
     */
    public function setRatingCount($rating_count)
    {
        $this->rating_count = $rating_count;
    }

    /**
     * @return int
     */
    public function getRatingCount()
    {
        return $this->rating_count;
    }

    /**
     * @param int $watching
     */
    public function setWatching($watching)
    {
        $this->watching = $watching;
    }

    /**
     * @return int
     */
    public function getWatching()
    {
        return $this->watching;
    }

    /**
     * @param int $watched
     */
    public function setWatched($watched)
    {
        $this->watched = $watched;
    }

    /**
     * @return int
     */
    public function getWatched()
    {
        return $this->watched;
    }

    /**
     * @param int $plans
     */
    public function setPlans($plans)
    {
        $this->plans = $plans;
    }

    /**
     * @return int
     */
    public function getPlans()
    {
        return $this->plans;
    }

    /**
     * @param int $stopped
     */
    public function setStopped($stopped)
    {
        $this->stopped = $stopped;
    }

    /**
     * @return int
     */
    public function getStopped()
    {
        return $this->stopped;
    }

    /**
     * @param int $abandoned
     */
    public function setAbandoned($abandoned)
    {
        $this->abandoned = $abandoned;
    }

    /**
     * @return int
     */
    public function getAbandoned()
    {
        return $this->abandoned;
    }

    /**
     * @param string $trailer
     */
    public function setTrailer($trailer)
    {
        $this->trailer = $trailer;
    }

    /**
     * @return string
     */
    public function getTrailer()
    {
        return $this->trailer;
    }
} 