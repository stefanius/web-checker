<?php

namespace Test\WebChecker\PageHelpers;

use Stefanius\WebChecker\PageHelpers\MetaDataHelper;
use Symfony\Component\DomCrawler\Crawler;

class MetaDataHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $filename
     * @param $pageTitle
     * @param $description
     * @param $hasRobotsFollow
     * @param $robotsIsFollow
     * @param $hasRobotsIndex
     * @param $robotsIsIndex
     *
     * @dataProvider dataProvider
     */
    public function testMetaDateHelperStringInput($filename, $pageTitle, $description, $hasRobotsFollow, $robotsIsFollow, $hasRobotsIndex, $robotsIsIndex)
    {
        $content = file_get_contents('/Users/sgrootveld/PhpstormProjects/web-checker/tests/testdata/' . $filename);

        $helper = new MetaDataHelper($content);

        $this->assertEquals($pageTitle, $helper->getPageTitle());
        $this->assertEquals($description, $helper->getDescription());
        $this->assertTrue($hasRobotsFollow === $helper->hasRobotsFollow());
        $this->assertTrue($robotsIsFollow === $helper->isRobotsFollow());
        $this->assertTrue($hasRobotsIndex === $helper->hasRobotsIndex());
        $this->assertTrue($robotsIsIndex === $helper->isRobotsIndex());
    }

    /**
     * @param $filename
     * @param $pageTitle
     * @param $description
     * @param $hasRobotsFollow
     * @param $robotsIsFollow
     * @param $hasRobotsIndex
     * @param $robotsIsIndex
     *
     * @dataProvider dataProvider
     */
    public function testMetaDateHelperCrawlerInput($filename, $pageTitle, $description, $hasRobotsFollow, $robotsIsFollow, $hasRobotsIndex, $robotsIsIndex)
    {
        $content = file_get_contents('/Users/sgrootveld/PhpstormProjects/web-checker/tests/testdata/' . $filename);
        $crawler = new Crawler($content);

        $helper = new MetaDataHelper($crawler);

        $this->assertEquals($pageTitle, $helper->getPageTitle());
        $this->assertEquals($description, $helper->getDescription());
        $this->assertTrue($hasRobotsFollow === $helper->hasRobotsFollow());
        $this->assertTrue($robotsIsFollow === $helper->isRobotsFollow());
        $this->assertTrue($hasRobotsIndex === $helper->hasRobotsIndex());
        $this->assertTrue($robotsIsIndex === $helper->isRobotsIndex());
    }

    public function dataProvider()
    {
        return [
            [
                'dvdw_1.html',
                'Alle dagen van de week welkom bij DagVanDeWeek.nl',
                'Het heden en verleden komen samen op DagVanDeWeek! Elke dag een nieuwe dag. Bekijk onze kalenders en laat het verleden naar vandaag komen!',
                true,
                true,
                true,
                true
            ],
            [
                'gm_1.html',
                'Gastouder Marjolein Dordrecht',
                'Gastouder Marjolein heeft een ruime ervaring met oppassen in Dodrecht. Als u een goede gastouder in Dordrecht zoekt, stop met zoeken!',
                true,
                true,
                true,
                true
            ],
        ];
    }
}
