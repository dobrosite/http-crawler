<?php

/**
 * DobroSite HTTP Crawler.
 *
 * @copyright 2019, ООО «Добро.сайт», http://добро.сайт
 */

namespace DobroSite\Crawler\HTTP\Source;

use DobroSite\Crawler\Document\Document;
use DobroSite\Crawler\HTTP\Document\Factory;
use DobroSite\Crawler\HTTP\Document\HTMLDocumentFactory;
use DobroSite\Crawler\HTTP\Document\SiteMapXMLDocumentFactory;
use DobroSite\Crawler\HTTP\Event\Source\ResponseEvent;
use DobroSite\Crawler\HTTP\SourceEvents;
use DobroSite\Crawler\Source\Source;
use Http\Client\HttpClient;
use Http\Message\RequestFactory;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Источник документов, работающий по HTTP.
 *
 * @since 0.1
 */
class HTTPSource implements Source
{
    /**
     * Диспетчер событий.
     *
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * Фабрики документов.
     *
     * @var Factory[]
     */
    private $factories;

    /**
     * Клиент HTTP.
     *
     * @var HttpClient
     */
    private $httpClient;

    /**
     * Фабрика запросов.
     *
     * @var RequestFactory
     */
    private $requestFactory;

    /**
     * Корневой URI.
     *
     * @var string
     */
    private $rootUri;

    /**
     * Создаёт клиента.
     *
     * @param string                        $rootUri         Корневой URI.
     * @param HttpClient                    $client          Клиент HTTP.
     * @param RequestFactory                $requestFactory  Фабрика запросов.
     * @param EventDispatcherInterface|null $eventDispatcher Диспетчер событий.
     *
     * @since 0.1
     */
    public function __construct(
        $rootUri,
        HttpClient $client,
        RequestFactory $requestFactory,
        EventDispatcherInterface $eventDispatcher = null
    ) {
        $this->rootUri = $rootUri;
        $this->httpClient = $client;
        $this->requestFactory = $requestFactory;
        $this->eventDispatcher = $eventDispatcher ?: new EventDispatcher();

        $this
            ->addDocumentFactory(new SiteMapXMLDocumentFactory())
            ->addDocumentFactory(new HTMLDocumentFactory());
    }

    /**
     * Добавляет фабрику документов.
     *
     * @param Factory $factory
     *
     * @return $this
     *
     * @since 0.1
     */
    public function addDocumentFactory(Factory $factory)
    {
        $this->factories[] = $factory;

        return $this;
    }

    /**
     * Добавляет слушателя событий.
     *
     * @param string   $eventName Имя события.
     * @param callable $listener  Слушатель.
     * @param int      $priority  Приоритет (больше значение — раньше будет вызван слушатель).
     *
     * @return $this
     *
     * @since 0.1
     */
    public function addEventListener($eventName, $listener, $priority = 0)
    {
        $this->eventDispatcher->addListener($eventName, $listener, $priority);

        return $this;
    }

    /**
     * Возвращает документ с указанным URI.
     *
     * @param string $uri
     *
     * @return Document
     *
     * @since 0.1
     */
    public function getDocument($uri)
    {
        $request = $this->requestFactory->createRequest('GET', $uri);
        $response = $this->httpClient->sendRequest($request);

        $event = new ResponseEvent($this, $response);
        $this->eventDispatcher->dispatch(SourceEvents::RESPONSE, $event);
        $response = $event->getResponse();

        foreach ($this->factories as $factory) {
            $document = $factory->create($this, $uri, $response);
            if ($document !== null) {
                return $document;
            }
        }

        throw new \RuntimeException('No such factory.');
    }

    /**
     * Возвращает корневой URI.
     *
     * @return string
     *
     * @since 0.1
     */
    public function rootUri()
    {
        return $this->rootUri;
    }
}
