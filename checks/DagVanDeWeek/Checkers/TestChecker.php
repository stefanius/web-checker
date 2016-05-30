<?php

namespace Checks\DagVanDeWeek\Checkers;

use Checks\DagVanDeWeek\DagVanDeWeekBase;
use Stefanius\WebChecker\Checker\Traits\SitemapsTrait;

class TestChecker extends DagVanDeWeekBase
{
    use SitemapsTrait;

    /**
     * @return array
     */
    public function sitemaps()
    {
        return [
            ['http://dagvandeweek.nl/sitemaps/history.xml'],
            ['http://dagvandeweek.nl/sitemaps/april-per-day.xml'],
            ['http://dagvandeweek.nl/sitemaps/augustus-per-day.xml'],
            ['http://dagvandeweek.nl/sitemaps/day.xml'],
            ['http://dagvandeweek.nl/sitemaps/december-per-day.xml'],
            ['http://dagvandeweek.nl/sitemaps/februari-per-day.xml'],
            ['http://dagvandeweek.nl/sitemaps/history.xml'],
            ['http://dagvandeweek.nl/sitemaps/januari-per-day.xml'],
            ['http://dagvandeweek.nl/sitemaps/juli-per-day.xml'],
            ['http://dagvandeweek.nl/sitemaps/juni-per-day.xml'],
            ['http://dagvandeweek.nl/sitemaps/maart-per-day.xml'],
            ['http://dagvandeweek.nl/sitemaps/mei-per-day.xml'],
            ['http://dagvandeweek.nl/sitemaps/november-per-day.xml'],
            ['http://dagvandeweek.nl/sitemaps/oktober-per-day.xml'],
            ['http://dagvandeweek.nl/sitemaps/page.xml'],
            ['http://dagvandeweek.nl/sitemaps/september-per-day.xml'],
        ];
    }
}