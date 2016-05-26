<?php

namespace Stefanius\WebChecker\Checker;

use GuzzleHttp\Client;
use Monolog\Logger;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Log\LoggerInterface;
use Stefanius\WebChecker\Checker\Traits\FormDataTrait;
use Stefanius\WebChecker\Checker\Traits\MetaDataTrait;
use Stefanius\WebChecker\Checker\Traits\MustContainHTagsTrait;
use Stefanius\WebChecker\Matchers\PlainTextMatcher;
use Stefanius\WebChecker\PageHelpers\MetaDataHelper;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Form;
use Psr\Http\Message\ResponseInterface;

abstract class WebCheck
{
    use MustContainHTagsTrait;

    use MetaDataTrait;

    use FormDataTrait;

    /**
     * @var Crawler
     */
    protected $crawler;

    /**
     * @var PlainTextMatcher
     */
    protected $matcher;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var Client
     */
    protected $client;

    protected $request;

    /**
     * @var string
     */
    protected $currentUri;

    protected $body;

    /**
     * @var MetaDataHelper
     */
    protected $metaDataHelper;

    protected $initialUri;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Make a request to the application and create a Crawler instance.
     *
     * @param  string  $method
     * @param  string  $uri
     * @param  array   $parameters
     * @param  array   $cookies
     * @param  array   $files
     *
     * @return $this
     */
    private function makeRequest($method, $uri, $parameters = [], $cookies = [], $files = [])
    {
        $this->initialUri = $uri;
        $this->currentUri = $uri;

        $onRedirect = function(
            RequestInterface $request,
            ResponseInterface $response,
            UriInterface $uri
        ) {
            $this->currentUri = sprintf('%s', $uri);
        };

        $options = [
            'allow_redirects' => [
                'max'             => 10,        // allow at most 10 redirects.
                'strict'          => true,      // use "strict" RFC compliant redirects.
                'referer'         => true,      // add a Referer header
                'on_redirect'     => $onRedirect,
                'track_redirects' => true
            ]
        ];
        $this->matcher = new PlainTextMatcher($this);
        $this->client = new Client();


        $this->request = $this->client->get($uri, $options);

        $this->response = $this->request;

        $this->body = $this->response->getBody()->getContents();

        $this->crawler = new Crawler();

        $this->crawler->addContent($this->body);
        $this->metaDataHelper = new MetaDataHelper($this->crawler);

        return $this;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Assert that the client response has an OK status code.
     *
     * @return $this
     */
    public function isResponseOk()
    {
        if (!($this->response->getStatusCode() === 200)) {
            $this->createError("Expected status code 200, got {$this->response->getStatusCode()}.");
        }

        return $this;
    }

    /**
     * Assert that the client response has a given code.
     *
     * @param  int  $code
     *
     * @return $this
     */
    public function isResponseStatus($code)
    {
        if (!($this->response->getStatusCode() === $code)) {
            $this->createError("Expected status code $code, got {$this->response->getStatusCode()}.");
        }

        return $this;
    }

    /**
     * Visit the given URI with a GET request.
     *
     * @param  string  $uri
     *
     * @return $this
     */
    public function visit($uri)
    {
        return $this->makeRequest('GET', $uri);
    }

    /**
     * Assert that the current page matches a given URI.
     *
     * @param  string  $uri
     * @return $this
     */
    protected function seePageIs($uri)
    {
        if ($this->currentUri !== $uri) {
            $this->createError($this->currentUri . ' is not the same as the expected uri: ' . $uri);
        }

        return $this;
    }

    /**
     * Assert that the current page matches a given URI.
     *
     * @return $this
     */
    protected function seePageIsRedirected()
    {
        if ($this->currentUri === $this->initialUri) {
            $this->createError('The page seems not to be redirected.');
        }

        return $this;
    }

    /**
     * Assert that a given string is seen on the page.
     *
     * @param  string $text
     *
     * @return $this
     */
    protected function see($text)
    {
        if(!$this->matcher->shouldContain($this->body, $text)) {
            $this->createError('hhjhjh');
        }

        return $this;
    }

    /**
     * Assert that a given string is not seen on the page.
     *
     * @param  string $text
     *
     * @return $this
     */
    protected function dontSee($text)
    {
        if(!$this->matcher->shouldNotContain($this->body, $text)) {
            $this->createError('Whoopsie doopsie');
        }

        return $this;
    }

    /**
     * Assert that a given string is seen inside an element.
     *
     * @param  string  $element
     * @param  string  $text
     *
     * @return $this
     */
    protected function seeInElement($element, $text)
    {
        $content = $this->crawler->filter($element)->html();

        if (!$this->matcher->shouldContain($content, $text)) {
            $this->createError('seeInElement');
        }

        return $this;
    }

    /**
     * Assert that a given string is not seen inside an element.
     *
     * @param  string  $text
     * @param  string  $element
     *
     * @return $this
     */
    protected function dontSeeInElement($element, $text)
    {
        $content = $this->crawler->filter($element)->html();

        $this->matcher->shouldNotContain($content, $text);

        return $this;
    }

    /**
     * Assert that a given link is seen on the page.
     *
     * @param  string  $text
     * @param  string|null  $url
     * @return $this
     */
    public function seeLink($text, $url = null)
    {
        $message = "No links were found with expected text [{$text}]";

        if ($url) {
            $message .= " and URL [{$url}]";
        }

        if (!$this->hasLink($text, $url)) {
            $this->createError($message);
        }

        return $this;
    }

    /**
     * Assert that a given link is not seen on the page.
     *
     * @param  string  $text
     * @param  string|null  $url
     * @return $this
     */
    public function dontSeeLink($text, $url = null)
    {
        $message = "A link was found with expected text [{$text}]";

        if ($url) {
            $message .= " and URL [{$url}]";
        }

        $this->assertFalse($this->hasLink($text, $url), "{$message}.");

        return $this;
    }

    /**
     * Check if the page has a link with the given $text and optional $url.
     *
     * @param  string  $text
     * @param  string|null  $url
     *
     * @return bool
     */
    protected function hasLink($text, $url = null)
    {
        $links = $this->crawler->selectLink($text);

        if ($links->count() == 0) {
            return false;
        }

        // If the URL is null, we assume the developer only wants to find a link
        // with the given text regardless of the URL. So, if we find the link
        // we will return true now. Otherwise, we look for the given URL.
        if ($url == null) {
            return true;
        }

        // $url = $this->addRootToRelativeUrl($url);

        foreach ($links as $link) {
            if ($link->getAttribute('href') == $url) {
                return true;
            }
        }

        return false;
    }

    /**
     * Assert that an input field contains the given value.
     *
     * @param  string  $selector
     * @param  string  $expected
     * @return $this
     */
    public function seeInField($selector, $expected)
    {
        $this->assertSame(
            $expected, $this->getInputOrTextAreaValue($selector),
            "The field [{$selector}] does not contain the expected value [{$expected}]."
        );

        return $this;
    }

    /**
     * Assert that an input field does not contain the given value.
     *
     * @param  string  $selector
     * @param  string  $value
     * @return $this
     */
    public function dontSeeInField($selector, $value)
    {
        $this->assertNotSame(
            $this->getInputOrTextAreaValue($selector), $value,
            "The input [{$selector}] should not contain the value [{$value}]."
        );

        return $this;
    }

    /**
     * Assert that the expected value is selected.
     *
     * @param  string  $selector
     * @param  string  $expected
     * @return $this
     */
    public function seeIsSelected($selector, $expected)
    {
        $this->assertEquals(
            $expected, $this->getSelectedValue($selector),
            "The field [{$selector}] does not contain the selected value [{$expected}]."
        );

        return $this;
    }

    /**
     * Assert that the given value is not selected.
     *
     * @param  string  $selector
     * @param  string  $value
     * @return $this
     */
    public function dontSeeIsSelected($selector, $value)
    {
        $this->assertNotEquals(
            $value, $this->getSelectedValue($selector),
            "The field [{$selector}] contains the selected value [{$value}]."
        );

        return $this;
    }

    /**
     * Get the value of an input or textarea.
     *
     * @param  string  $selector
     * @return string
     *
     * @throws \Exception
     */
    protected function getInputOrTextAreaValue($selector)
    {
        $field = $this->filterByNameOrId($selector, ['input', 'textarea']);

        if ($field->count() == 0) {
            throw new \Exception("There are no elements with the name or ID [$selector].");
        }

        $element = $field->nodeName();

        if ($element == 'input') {
            return $field->attr('value');
        }

        if ($element == 'textarea') {
            return $field->text();
        }

        throw new \Exception("Given selector [$selector] is not an input or textarea.");
    }

    /**
     * Click a link with the given body, name, or ID attribute.
     *
     * @param  string  $name
     * @return $this
     */
    protected function click($name)
    {
        $link = $this->crawler->selectLink($name);

        if (! count($link)) {
            $link = $this->filterByNameOrId($name, 'a');

            if (! count($link)) {
                throw new \InvalidArgumentException(
                    "Could not find a link with a body, name, or ID attribute of [{$name}]."
                );
            }
        }

        $this->visit($link->link()->getUri());

        return $this;
    }

    /**
     * Assert that a filtered Crawler returns nodes.
     *
     * @param  string  $filter
     * @return void
     */
    protected function assertFilterProducesResults($filter)
    {
        $crawler = $this->filterByNameOrId($filter);

        if (! count($crawler)) {
            throw new \InvalidArgumentException(
                "Nothing matched the filter [{$filter}] CSS query provided for [{$this->currentUri}]."
            );
        }
    }

    /**
     * Filter elements according to the given name or ID attribute.
     *
     * @param  string  $name
     * @param  array|string  $elements
     *
     * @return Crawler
     */
    protected function filterByNameOrId($name, $elements = '*')
    {
        $name = str_replace('#', '', $name);

        $id = str_replace(['[', ']'], ['\\[', '\\]'], $name);

        $elements = is_array($elements) ? $elements : [$elements];

        array_walk($elements, function (&$element) use ($name, $id) {
            $element = "{$element}#{$id}, {$element}[name='{$name}']";
        });

        return $this->crawler->filter(implode(', ', $elements));
    }

    protected function createError($msg)
    {
        $this->logger->info($msg);
    }
}
