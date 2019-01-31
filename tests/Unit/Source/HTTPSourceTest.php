<?php

/**
 * DobroSite HTTP Crawler.
 *
 * @copyright 2019, ООО «Добро.сайт», http://добро.сайт
 */

namespace DobroSite\Crawler\HTTP\Tests\Unit\Source;

use DobroSite\Crawler\HTTP\Document\Factory;
use DobroSite\Crawler\HTTP\Source\HTTPSource;
use Http\Client\HttpClient;
use Http\Message\RequestFactory;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Тесты источника документов, работающего по HTTP..
 *
 * @covers \DobroSite\Crawler\HTTP\Source\HTTPSource
 */
class HTTPSourceTest extends TestCase
{
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
     * Готовит окружение теста.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->eventDispatcher = $this->getMockForAbstractClass(EventDispatcherInterface::class);
        $this->httpClient = $this->getMockForAbstractClass(HttpClient::class);
        $this->requestFactory = $this->getMockForAbstractClass(RequestFactory::class);
    }
}
