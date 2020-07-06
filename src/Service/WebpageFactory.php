<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Webpage;

class WebpageFactory
{
    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function createPage(string $url): ?Webpage
    {
        try {
            $response = $this->httpClient->request('GET', $url);
            $pageContent = $response->getContent();
        } catch (RedirectionExceptionInterface|ClientExceptionInterface|ServerExceptionInterface $exception) {
            return null;
        } catch (TransportExceptionInterface $exception) {
            return null;
        }

        return new Webpage($url, $pageContent);
    }
}