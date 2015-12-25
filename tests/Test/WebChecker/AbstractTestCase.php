<?php

namespace Test\WebChecker;

class AbstractTestCase extends \PHPUnit_Framework_TestCase
{
    protected function loadTestData($filename)
    {
        return file_get_contents($this->getBasePath() . '/testdata/' . $filename);
    }

    protected function getBasePath()
    {
        return dirname(dirname(dirname(__FILE__)));
    }
}