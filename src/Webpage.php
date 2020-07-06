<?php

namespace App;

class Webpage
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $html;

    public function __construct(string $url, string $html)
    {
        $this->url = $url;
        $this->html = $html;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getHtml(): string
    {
        return $this->html;
    }
}