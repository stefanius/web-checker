<?php

namespace Stefanius\WebChecker\Checker\Traits;

trait MustContainHTagsTrait
{
    /**
     * @param $text
     *
     * @return $this
     */
    protected function mustContainH1($text)
    {
        return $this->seeInElement('H1', $text);
    }

    /**
     * @param $text
     *
     * @return $this
     */
    protected function mustContainH2($text)
    {
        return $this->seeInElement('H2', $text);
    }

    /**
     * @param $text
     *
     * @return $this
     */
    protected function mustContainH3($text)
    {
        return $this->seeInElement('H3', $text);
    }

    /**
     * @param $text
     *
     * @return $this
     */
    protected function mustContainH4($text)
    {
        return $this->seeInElement('H4', $text);
    }

    /**
     * @param $text
     *
     * @return $this
     */
    protected function mustContainH5($text)
    {
        return $this->seeInElement('H5', $text);
    }

    /**
     * @param $text
     *
     * @return $this
     */
    protected function mustContainH6($text)
    {
        return $this->seeInElement('H6', $text);
    }
}