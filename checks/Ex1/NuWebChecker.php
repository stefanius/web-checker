<?php

use Stefanius\WebChecker\Annotations\DataProvider;

class NuWebChecker extends Stefanius\WebChecker\Checker\WebCheck
{
    public function checkDvdw()
    {
        $this->visit('dagvandeweek.nl')
            ->assertResponseOk()
            ->assertResponseStatus(200)
            ->pageTitleIs('Alle dagen van de week welkom bij DagVanDeWeek.nl')
            ->hasRobotsIndex();
    }

    public function checkNu()
    {
        return;
        $this->visit('http://www.nu.nl')
            ->assertResponseOk()
            ->assertResponseStatus(200)
            ->see('Zaterdag 12 december 2015')
            ->mustContainH1('195 landen stemmen in met wereldwijd klimaatverdrag Parijs')
            ->descriptionIs('Het laatste nieuws het eerst op NU.nl');
    }

    /**
     * @dataProvider henk
     */
    public function checkWithDataProvider($p1, $p2, $p3)
    {

    }

    public function henk()
    {
        return [
            ['1', '2', '3'],
            ['uno', 'dos', 'dres'],
            ['aap', 'noot', 'mies'],
        ];
    }
}