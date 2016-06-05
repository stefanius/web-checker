<?php

namespace Stefanius\WebChecker\Checker\Traits;

use Stefanius\WebChecker\PageHelpers\MetaDataHelper;

trait MetaDataTrait
{
    /**
     * @var MetaDataHelper
     */
    protected $metaDataHelper;

    /**
     * @param $title
     *
     * @return $this
     */
    protected function pageTitleIs($title)
    {
        if ($this->metaDataHelper->getPageTitle() !== $title) {
            $this->createError(sprintf("The pagetitle '%s' does not match the expected pagetitle '%s'",
                $this->metaDataHelper->getPageTitle(),
                $title
            ));
        }

        return $this;
    }

    /**
     * @param $description
     *
     * @return $this
     */
    protected function descriptionIs($description)
    {
        $foundDescription = $this->metaDataHelper->getDescription();

        if ($foundDescription !== $description) {
            $this->createError('wrong description');
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function hasRobotsIndex()
    {
        if (!$this->metaDataHelper->hasRobotsIndex()) {
            $this->createError("Page fails on 'hasRobotsIndex'");
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function hasRobotsNoIndex()
    {
        if (!$this->metaDataHelper->hasRobotsNoIndex()) {
            $this->createError("Page fails on 'hasRobotsNoIndex'");
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function hasRobotsNoFollow()
    {
        if (!$this->metaDataHelper->hasRobotsNoFollow()) {
            $this->createError("Page fails on 'hasRobotsNoFollow'");
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function hasRobotsFollow()
    {
        if (!$this->metaDataHelper->hasRobotsFollow()) {
            $this->createError("Page fails on 'hasRobotsFollow'");
        }

        return $this;
    }

    /**
     * @param $min
     * @param $max
     *
     * @return $this
     */
    protected function metaDescriptionBetween($min, $max)
    {
        $description = $this->metaDataHelper->getDescription();

        if (!((strlen($description) >= $min) && (strlen($description) <= $max))) {
            $this->createError("Page description is not between $min and $max characters. Current length is: '" . strlen($description) . "'");
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function hasGoodMetaDescription()
    {
        return $this->metaDescriptionBetween(140, 160);
    }

    /**
     * @return $this
     */
    protected function hasMetaDescription()
    {
        if (!$this->metaDataHelper->hasDescription()) {
            $this->createError("Page description does not contain a metadescription.");
        }

        return $this;
    }

    /**
     * @param $needle
     * 
     * @return $this
     */
    protected function metaDescriptionContains($needle)
    {
        $description = $this->metaDataHelper->getDescription();

        if (strpos(strtolower($description), strtolower($needle)) === false) {
            $this->createError("Page metadescription does not contain '$needle'");
        }

        return $this;
    }
}
