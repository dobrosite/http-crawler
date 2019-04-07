<?php

/**
 * DobroSite HTTP Crawler.
 *
 * @copyright 2019, ООО «Добро.сайт», http://добро.сайт
 */

namespace DobroSite\Crawler\HTTP\Source;

use DobroSite\Crawler\Document\Document;
use DobroSite\Crawler\Exception\Source\ReadException;
use DobroSite\Crawler\HTTP\Document\Factory;
use DobroSite\Crawler\HTTP\Event\Source\ResponseEvent;
use DobroSite\Crawler\HTTP\SourceEvents;
use DobroSite\Crawler\Source\Source;
use GuzzleHttp\Psr7\Uri;
use Http\Client\Exception as HttpClientException;
use Http\Client\HttpClient;
use Http\Message\RequestFactory;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\EventDispatcher\Event;
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
     * @var EventDispatcherInterface|null
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
     * @param string                        $rootUri           Корневой URI.
     * @param HttpClient                    $client            Клиент HTTP.
     * @param RequestFactory                $requestFactory    Фабрика запросов.
     * @param Factory[]                     $documentFactories Фабрики документов.
     * @param EventDispatcherInterface|null $eventDispatcher   Диспетчер событий.
     *
     * @since 0.3 Добавлен аргумент $documentFactories.
     * @since 0.1
     */
    public function __construct(
        $rootUri,
        HttpClient $client,
        RequestFactory $requestFactory,
        array $documentFactories,
        EventDispatcherInterface $eventDispatcher = null
    ) {
        $this->rootUri = $rootUri;
        $this->httpClient = $client;
        $this->requestFactory = $requestFactory;
        $this->factories = $documentFactories;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Возвращает документ с указанным URI.
     *
     * @param string $uri
     *
     * @return Document
     *
     * @throws \RuntimeException Если нет подходящей фабрики.
     * @throws ReadException Если не удалось прочитать данные.
     *
     * @since 0.2 вбрасывает исключение ReadException если не удалось прочитать данные.
     * @since 0.1
     */
    public function getDocument($uri)
    {
        $response = $this->sendHttpRequest($uri);

        $event = new ResponseEvent($this, $response);
        $this->dispatchEvent(SourceEvents::RESPONSE, $event);
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

    /**
     * Отправляет извещение о событии.
     *
     * @param string $eventName
     * @param Event  $event
     *
     * @return void
     */
    private function dispatchEvent($eventName, Event $event)
    {
        if ($this->eventDispatcher !== null) {
            $this->eventDispatcher->dispatch($eventName, $event);
        }
    }

    /**
     * Выполняет запрос по HTTP указанного URI.
     *
     * @param string $uri
     *
     * @return ResponseInterface
     *
     * @throws ReadException Если не удалось прочитать данные.
     */
    private function sendHttpRequest($uri)
    {
        $request = $this->requestFactory->createRequest('GET', $uri);
        try {
            $response = $this->httpClient->sendRequest($request);
        } catch (HttpClientException $exception) {
            throw new ReadException(
                sprintf('Can not read document "%s". %s', $uri, $exception->getMessage()),
                $this,
                0,
                $exception
            );
        } catch (\Exception $exception) {
            throw new ReadException(
                sprintf('Can not read document "%s". %s', $uri, $exception->getMessage()),
                $this,
                0,
                $exception
            );
        }

        if ((int) floor($response->getStatusCode() / 100) === 3) {
            $oldUri = new Uri($uri);
            $newUri = new Uri($response->getHeaderLine('Location'));
            if ($newUri->getHost() === '') {
                $newUri = $newUri->withHost($oldUri->getHost());
            }
            if ($newUri->getScheme() === '') {
                $newUri = $newUri->withScheme($oldUri->getScheme());
            }
            $response = $this->sendHttpRequest($newUri);
        }

        // TODO Обработка ошибок (4xx, 5xx).

        return $response;
    }
}
