<?php

namespace Checks\DagVanDeWeek\Checkers;

use Checks\DagVanDeWeek\DagVanDeWeekBase;

class TestChecker extends DagVanDeWeekBase
{
    /**
     * @dataProvider sitemaps
     *
     * @param $sitemap
     */
    public function checkSitemapsReacheble($sitemap)
    {
        $this->visit($sitemap)
            ->isResponseOk();
    }
    
    /**
     * @dataProvider sitemaps
     *
     * @param $sitemap
     */
    public function checkSitemapTest($sitemap)
    {
        $sitemap = simplexml_load_file($sitemap);

        foreach ($sitemap as $item) {
            $url = (string)trim($item->loc);

            $this->visit($url)
                ->hasRobotsFollow()
                ->hasRobotsIndex();
        }
    }

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