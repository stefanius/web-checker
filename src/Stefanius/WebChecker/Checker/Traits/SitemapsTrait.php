<?php

namespace Stefanius\WebChecker\Checker\Traits;

trait SitemapsTrait
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
                ->hasRobotsIndex()
                ->hasMetaDescription()
                ->hasGoodMetaDescription();
        }
    }

    /**
     * Implement this method to test your sitemapped urls.
     *
     * @return array
     */
    abstract public function sitemaps();
}
