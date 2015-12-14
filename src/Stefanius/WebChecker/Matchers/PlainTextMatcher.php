<?php

namespace Stefanius\WebChecker\Matchers;

class PlainTextMatcher
{
    /**
     * Test if a haystack (text) contains the needle.
     *
     * @param $haystack
     * @param $needle
     *
     * @return bool
     */
    public function shouldContain($haystack, $needle)
    {
        $pattern = $this->formatPattern($needle);

        return preg_match($pattern, $haystack);
    }

    /**
     * Test if a haystack (text) does not contains the needle.
     *
     * @param $haystack
     * @param $needle
     *
     * @return bool
     */
    public function shouldNotContain($haystack, $needle)
    {
        $pattern = $this->formatPattern($needle);

        return !preg_match($pattern, $haystack);
    }

    /**
     * Format the regex pattern.
     *
     * @param string $needle
     *
     * @return string
     */
    protected function formatPattern($needle)
    {
        $rawPattern = preg_quote($needle, '/');

        $escapedPattern = preg_quote($needle, '/'); //write Escape function.

        $pattern = $rawPattern == $escapedPattern
            ? $rawPattern : "({$rawPattern}|{$escapedPattern})";

        return '/' . $pattern . '/';
    }
}
