<?php

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
}