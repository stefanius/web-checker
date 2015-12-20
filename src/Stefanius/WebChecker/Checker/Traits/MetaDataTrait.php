<?php

namespace Stefanius\WebChecker\Checker\Traits;

trait MetaDataTrait
{
    /**
     * @param $title
     *
     * @return $this
     */
    protected function pageTitleIs($title)
    {
        if ($this->metaDataHelper->getPageTitle() !== $title) {
            $this->createError('henk');
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
            $this->createError("blah");
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function hasRobotsNoIndex()
    {
        if (!$this->metaDataHelper->hasRobotsNoIndex()) {
            $this->createError("blah");
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function hasRobotsNoFollow()
    {
        if (!$this->metaDataHelper->hasRobotsNoFollow()) {
            $this->createError("blah");
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function hasRobotsFollow()
    {
        if (!$this->metaDataHelper->hasRobotsFollow()) {
            $this->createError("blah");
        }

        return $this;
    }
}