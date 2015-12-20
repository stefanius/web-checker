<?php

namespace Stefanius\WebChecker\Checker\Traits;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Form;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait FormDataTrait
{
    /**
     * @var array
     */
    protected $inputs;

    /**
     * @var array
     */
    protected $uploads;

    /**
     * Submit a form using the button with the given text value.
     *
     * @param  string  $buttonText
     *
     * @return $this
     */
    protected function press($buttonText)
    {
        return $this->submitForm($buttonText, $this->inputs, $this->uploads);
    }

    /**
     * Submit a form on the page with the given input.
     *
     * @param  string  $buttonText
     * @param  array  $inputs
     * @param  array  $uploads
     * @return $this
     */
    protected function submitForm($buttonText, $inputs = [], $uploads = [])
    {
        $this->makeRequestUsingForm($this->fillForm($buttonText, $inputs), $uploads);

        return $this;
    }

    /**
     * Fill the form with the given data.
     *
     * @param  string  $buttonText
     * @param  array  $inputs
     * @return \Symfony\Component\DomCrawler\Form
     */
    protected function fillForm($buttonText, $inputs = [])
    {
        if (! is_string($buttonText)) {
            $inputs = $buttonText;

            $buttonText = null;
        }

        return $this->getForm($buttonText)->setValues($inputs);
    }

    /**
     * Get the form from the page with the given submit button text.
     *
     * @param  string|null  $buttonText
     * @return \Symfony\Component\DomCrawler\Form
     */
    protected function getForm($buttonText = null)
    {
        try {
            if ($buttonText) {
                return $this->crawler->selectButton($buttonText)->form();
            }

            return $this->crawler->filter('form')->form();
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException(
                "Could not find a form that has submit button [{$buttonText}]."
            );
        }
    }

    /**
     * Store a form input in the local array.
     *
     * @param  string  $element
     * @param  string  $text
     *
     * @return $this
     */
    protected function storeInput($element, $text)
    {
        $this->assertFilterProducesResults($element);

        $element = str_replace('#', '', $element);

        $this->inputs[$element] = $text;

        return $this;
    }

    /**
     * Fill an input field with the given text.
     *
     * @param  string  $text
     * @param  string  $element
     *
     * @return $this
     */
    protected function type($text, $element)
    {
        return $this->storeInput($element, $text);
    }

    /**
     * Check a checkbox on the page.
     *
     * @param  string  $element
     *
     * @return $this
     */
    protected function check($element)
    {
        return $this->storeInput($element, true);
    }

    /**
     * Uncheck a checkbox on the page.
     *
     * @param  string  $element
     *
     * @return $this
     */
    protected function uncheck($element)
    {
        return $this->storeInput($element, false);
    }

    /**
     * Select an option from a drop-down.
     *
     * @param  string  $option
     * @param  string  $element
     *
     * @return $this
     */
    protected function select($option, $element)
    {
        return $this->storeInput($element, $option);
    }



    /**
     * Convert the given uploads to UploadedFile instances.
     *
     * @param  Form   $form
     * @param  array  $uploads
     *
     * @return array
     */
    protected function convertUploadsForTesting(Form $form, array $uploads)
    {
        $files = $form->getFiles();

        $names = array_keys($files);

        $files = array_map(function (array $file, $name) use ($uploads) {
            return isset($uploads[$name])
                ? $this->getUploadedFileForTesting($file, $uploads, $name)
                : $file;
        }, $files, $names);

        return array_combine($names, $files);
    }

    /**
     * Create an UploadedFile instance for testing.
     *
     * @param  array  $file
     * @param  array  $uploads
     * @param  string $name
     *
     * @return UploadedFile
     */
    protected function getUploadedFileForTesting($file, $uploads, $name)
    {
        return new UploadedFile(
            $file['tmp_name'], basename($uploads[$name]), $file['type'], $file['size'], $file['error'], true
        );
    }

    /**
     * Attach a file to a form field on the page.
     *
     * @param  string  $absolutePath
     * @param  string  $element
     *
     * @return $this
     */
    protected function attach($absolutePath, $element)
    {
        $this->uploads[$element] = $absolutePath;

        return $this->storeInput($element, $absolutePath);
    }

    /**
     * Assert that the given checkbox is selected.
     *
     * @param  string  $selector
     * @return $this
     */
    public function seeIsChecked($selector)
    {
        if (!$this->isChecked($selector)) {
            $this->msg("The checkbox [{$selector}] is not checked.");
        }

        return $this;
    }

    /**
     * Assert that the given checkbox is not selected.
     *
     * @param  string  $selector
     * @return $this
     */
    public function dontSeeIsChecked($selector)
    {
        if ($this->isChecked($selector)) {
            $this->msg("The checkbox [{$selector}] is checked.");
        }

        return $this;
    }

    /**
     * Make a request to the application using the given form.
     *
     * @param  \Symfony\Component\DomCrawler\Form  $form
     * @param  array  $uploads
     *
     * @return $this
     */
    protected function makeRequestUsingForm(Form $form, array $uploads = [])
    {
        $files = $this->convertUploadsForTesting($form, $uploads);

        return $this->makeRequest(
            $form->getMethod(), $form->getUri(), $this->extractParametersFromForm($form), [], $files
        );
    }

    /**
     * Get the selected value from a select field.
     *
     * @param  \Symfony\Component\DomCrawler\Crawler  $field
     *
     * @return string|null
     *
     * @throws \Exception
     */
    protected function getSelectedValueFromSelect(Crawler $field)
    {
        if ($field->nodeName() !== 'select') {
            throw new \Exception('Given element is not a select element.');
        }

        foreach ($field->children() as $option) {
            if ($option->hasAttribute('selected')) {
                return $option->getAttribute('value');
            }
        }

        return null;
    }

    /**
     * Return true if the given checkbox is checked, false otherwise.
     *
     * @param  string  $selector
     * @return bool
     *
     * @throws \Exception
     */
    protected function isChecked($selector)
    {
        $checkbox = $this->filterByNameOrId($selector, "input[type='checkbox']");

        if ($checkbox->count() == 0) {
            throw new \Exception("There are no checkbox elements with the name or ID [$selector].");
        }

        return $checkbox->attr('checked') !== null;
    }

    /**
     * Get the selected value of a select field or radio group.
     *
     * @param  string  $selector
     * @return string|null
     *
     * @throws \Exception
     */
    protected function getSelectedValue($selector)
    {
        $field = $this->filterByNameOrId($selector);

        if ($field->count() == 0) {
            throw new \Exception("There are no elements with the name or ID [$selector].");
        }

        $element = $field->nodeName();

        if ($element == 'select') {
            return $this->getSelectedValueFromSelect($field);
        }

        if ($element == 'input') {
            return $this->getCheckedValueFromRadioGroup($field);
        }

        throw new \Exception("Given selector [$selector] is not a select or radio group.");
    }

    /**
     * Get the checked value from a radio group.
     *
     * @param  \Symfony\Component\DomCrawler\Crawler  $radioGroup
     * @return string|null
     *
     * @throws \Exception
     */
    protected function getCheckedValueFromRadioGroup(Crawler $radioGroup)
    {
        if ($radioGroup->nodeName() !== 'input' || $radioGroup->attr('type') !== 'radio') {
            throw new \Exception('Given element is not a radio button.');
        }

        foreach ($radioGroup as $radio) {
            if ($radio->hasAttribute('checked')) {
                return $radio->getAttribute('value');
            }
        }

        return null;
    }
}