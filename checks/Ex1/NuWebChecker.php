<?php

class NuWebChecker extends Stefanius\WebChecker\Checker\WebCheck
{
    public function doSomeThing()
    {
        $this->visit('http://www.nu.nl')
            ->assertResponseOk()
            ->assertResponseStatus(200)
            ->see('Zaterdag 12 december 2015')
            ->mustContainH1('195 landen stemmen in met wereldwijd klimaatverdrag Parijs')
            ->descriptionIs('Het laatste nieuws het eerst op NU.nl');
    }

    public function checkHallo()
    {
        echo 'aaaa';
    }
}