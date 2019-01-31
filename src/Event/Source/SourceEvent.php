<?php

/**
 * DobroSite HTTP Crawler.
 *
 * @copyright 2019, ООО «Добро.сайт», http://добро.сайт
 */

namespace DobroSite\Crawler\HTTP\Event\Source;

use DobroSite\Crawler\HTTP\Source\HTTPSource;
use Symfony\Component\EventDispatcher\Event;

/**
 * Событие источника данных.
 *
 * @since 0.1
 */
class SourceEvent extends Event
{
    /**
     * Источник.
     *
     * @var HTTPSource
     */
    private $source;

    /**
     * Создаёт событие.
     *
     * @param HTTPSource $source
     *
     * @since 0.1
     */
    public function __construct(HTTPSource $source)
    {
        $this->source = $source;
    }

    /**
     * Возвращает источник события.
     *
     * @return HTTPSource
     *
     * @since 0.1
     */
    public function getResponse()
    {
        return $this->source;
    }
}
