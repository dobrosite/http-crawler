<?php

/**
 * DobroSite HTTP Crawler.
 *
 * @copyright 2019, ООО «Добро.сайт», http://добро.сайт
 */

namespace DobroSite\Crawler\HTTP\Event\Source;

use DobroSite\Crawler\HTTP\Source\HTTPSource;
use Psr\Http\Message\ResponseInterface;

/**
 * Событие, связанное с ответом HTTP.
 *
 * @since 0.1
 */
class ResponseEvent extends SourceEvent
{
    /**
     * Ответ HTTP.
     *
     * @var ResponseInterface
     */
    private $response;

    /**
     * Создаёт событие.
     *
     * @param HTTPSource        $source
     * @param ResponseInterface $response
     *
     * @since 0.1
     */
    public function __construct(HTTPSource $source, ResponseInterface $response)
    {
        parent::__construct($source);

        $this->response = $response;
    }

    /**
     * Возвращает ответ HTTP.
     *
     * @return ResponseInterface
     *
     * @since 0.1
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Задаёт ответ HTTP.
     *
     * @param ResponseInterface $response
     *
     * @return void
     *
     * @since 0.1
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
    }
}
