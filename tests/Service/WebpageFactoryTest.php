<?php

namespace App\Tests\Service;

use App\Webpage;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\Exception\RedirectionException;
use Symfony\Component\HttpClient\Exception\ServerException;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use App\Service\WebpageFactory;

class WebpageFactoryTest extends TestCase
{
    private const URL = 'https://foo.bar';

    public function testWebpageCreated(): void
    {
        $html = '<div>Foobar</div>';

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->method('getContent')
            ->willReturn($html);

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->method('request')
            ->willReturn($response);

        $resultingWebpage = (new WebpageFactory($httpClient))->createPage(self::URL);

        $validWebpage = new Webpage(self::URL, $html);

        $this->assertEqualsCanonicalizing($validWebpage, $resultingWebpage);
    }

    public function testFailedRequest(): void
    {
        $this->makeFailingRequest(new TransportException());
    }

    public function testUrlWithRedirect(): void
    {
        $this->makeFailingRequest(new RedirectionException(new MockResponse()));
    }

    public function testUrlWithClientError(): void
    {
        $this->makeFailingRequest(new ClientException(new MockResponse()));
    }

    public function testUrlWithServiceError(): void
    {
        $this->makeFailingRequest(new ServerException(new MockResponse()));
    }

    private function makeFailingRequest(\Throwable $thrownException): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->method('request')
            ->willThrowException($thrownException);

        $webpage = (new WebpageFactory($httpClient))->createPage(self::URL);
        $this->assertNull($webpage);
    }
}