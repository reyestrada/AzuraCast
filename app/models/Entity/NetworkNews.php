<?php
namespace Entity;

use \Doctrine\Common\Collections\ArrayCollection;

/**
 * @Table(name="network_news")
 * @Entity
 */
class NetworkNews extends \DF\Doctrine\Entity
{
    public function __construct()
    {
        $this->layout = 'horizontal';
        $this->timestamp = time();
        $this->tags = array();
    }

    /**
     * @Column(name="guid", type="string", length=128)
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    protected $id;

    /** @Column(name="title", type="string", length=400) */
    protected $title;

    /** @Column(name="layout", type="string", length=50) */
    protected $layout;

    /** @Column(name="body", type="text", nullable=true) */
    protected $body;

    /** @Column(name="image_url", type="string", length=100, nullable=true) */
    protected $image_url;

    /** @Column(name="web_url", type="string", length=100, nullable=true) */
    protected $web_url;

    /** @Column(name="timestamp", type="integer") */
    protected $timestamp;

    /** @Column(name="tags", type="json", nullable=true) */
    protected $tags;

    /**
     * Static Functions
     */

    public static function fetchFeatured()
    {
        $news = \DF\Cache::get('homepage_featured_news');

        if (!$news)
        {
            $news = self::fetch(7);
            \DF\Cache::save($news, 'homepage_featured_news', null, 300);
        }

        return $news;
    }

    public static function fetch($articles_num = 5)
    {
        $em = self::getEntityManager();
        $results_raw = $em->createQuery('SELECT nn FROM '.__CLASS__.' nn ORDER BY nn.timestamp DESC')
            ->getArrayResult();

        $network_news = array();
        foreach($results_raw as $row)
        {
            $row['image_url'] = \DF\Url::content($row['image_url']);
            $network_news[] = $row;
        }

        if ($articles_num)
            $network_news = array_slice($network_news, 0, $articles_num);

        return $network_news;
    }
}