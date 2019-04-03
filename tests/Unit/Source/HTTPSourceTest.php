<?php

/**
 * DobroSite HTTP Crawler.
 *
 * @copyright 2019, ООО «Добро.сайт», http://добро.сайт
 */

namespace DobroSite\Crawler\HTTP\Tests\Unit\Source;

use DobroSite\Crawler\HTTP\Source\HTTPSource;
use Http\Client\HttpClient;
use Http\Message\RequestFactory;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Тесты источника документов, работающего по HTTP.
 *
 * @covers \DobroSite\Crawler\HTTP\Source\HTTPSource
 */
class HTTPSourceTest extends TestCase
{
    /**
     * Корневой адрес источника.
     */
    const ROOT_URI = 'http://example.com/';

    /**
     * Диспетчер событий.
     *
     * @var EventDispatcherInterface|MockObject
     */
    private $eventDispatcher;

    /**
     * Клиент HTTP.
     *
     * @var HttpClient|MockObject
     */
    private $httpClient;

    /**
     * Фабрика запросов.
     *
     * @var RequestFactory|MockObject
     */
    private $requestFactory;

    /**
     * Проверяемый источник данных.
     *
     * @var HTTPSource
     */
    private $source;

    /**
     * Проверяет следование перенаправлениям.
     *
     * @throws \Exception
     */
    public function testFollowsRedirects()
    {
        $uri1 = self::ROOT_URI . 'foo';
        $request1 = $this->createMock(RequestInterface::class);
        $response1 = $this->createConfiguredMock(
            ResponseInterface::class,
            [
                'getStatusCode' => 301,
                'getHeaderLine' => '/bar'
            ]
        );

        $uri2 = self::ROOT_URI . 'bar';
        $request2 = $this->createMock(RequestInterface::class);
        $response2 = $this->createConfiguredMock(
            ResponseInterface::class,
            [
                'getStatusCode' => 200,
                'getHeaderLine' => 'text/html',
                'getBody' => $this->createConfiguredMock(
                    StreamInterface::class,
                    ['__toString' => '<html/>']
                )
            ]
        );

        $this->requestFactory
            ->expects(self::at(0))
            ->method('createRequest')
            ->with('GET', $uri1)
            ->willReturn($request1);

        $this->httpClient
            ->expects(self::at(0))
            ->method('sendRequest')
            ->with(self::identicalTo($request1))
            ->willReturn($response1);

        $this->requestFactory
            ->expects(self::at(1))
            ->method('createRequest')
            ->with('GET', $uri2)
            ->willReturn($request2);

        $this->httpClient
            ->expects(self::at(1))
            ->method('sendRequest')
            ->with(self::identicalTo($request2))
            ->willReturn($response2);

        $this->source->getDocument(self::ROOT_URI . 'foo');
    }

    /**
     * Готовит окружение теста.
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function setUp()
    {
        parent::setUp();

        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->httpClient = $this->createMock(HttpClient::class);
        $this->requestFactory = $this->createMock(RequestFactory::class);
        $this->source = new HTTPSource(self::ROOT_URI, $this->httpClient, $this->requestFactory);
    }
}
