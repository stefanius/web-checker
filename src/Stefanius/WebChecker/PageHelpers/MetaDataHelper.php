<?php

namespace Stefanius\WebChecker\PageHelpers;

use Symfony\Component\DomCrawler\Crawler;

class MetaDataHelper
{
    /**
     * @var Crawler
     */
    protected $crawler;

    /**
     * MetaDataHelper constructor.
     *
     * @param string|Crawler $data
     *
     * @throws \Exception
     */
    public function __construct($data)
    {
        if (!($data instanceof Crawler) && !(is_string($data))) {
            throw new \Exception('Parameter must be either a string or an instance of \Symfony\Component\DomCrawler\Crawler\'.');
        }

        if ($data instanceof Crawler) {
            $this->crawler = $data;
        }

        if (is_string($data)) {
            $this->crawler = new Crawler();
            $this->crawler->addContent($data);
        }
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        if ($this->crawler->filterXPath("//meta[@name='description']")->count()) {
            return $this->crawler->filterXPath("//meta[@name='description']")->attr('content');
        }

        return '';
    }

    /**
     * @return null|string
     */
    public function getPageTitle()
    {
        return $this->crawler->filterXPath("//title")->html();
    }

    /**
     * @return array
     */
    public function getRobotsData()
    {
        $data = strtolower($this->crawler->filterXPath("//meta[@name='robots']")->attr('content'));
        $splitted = explode(',', $data);
        $robots = [];

        foreach ($splitted as $value) {
            $value = trim($value);

            if (strlen($value) > 2) {
                $robots[$value] = $value;
            }
        }

        return $robots;
    }

    /**
     * @return bool
     */
    public function isRobotsFollow()
    {
        $robotsData = $this->getRobotsData();

        if (array_key_exists('nofollow', $robotsData)) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isRobotsIndex()
    {
        $robotsData = $this->getRobotsData();

        if (array_key_exists('noindex', $robotsData)) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function hasRobotsIndex()
    {
        return array_key_exists('index', $this->getRobotsData());
    }

    /**
     * @return bool
     */
    public function hasRobotsNoIndex()
    {
        return array_key_exists('noindex', $this->getRobotsData());
    }

    /**
     * @return bool
     */
    public function hasRobotsNoFollow()
    {
        return array_key_exists('nofollow', $this->getRobotsData());
    }

    /**
     * @return bool
     */
    public function hasRobotsFollow()
    {
        return array_key_exists('follow', $this->getRobotsData());
    }
}