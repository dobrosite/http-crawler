<?php

/**
 * DobroSite HTTP Crawler.
 *
 * @copyright 2019, ООО «Добро.сайт», http://добро.сайт
 */

namespace DobroSite\Crawler\HTTP\Document;

use DobroSite\Crawler\Document\Document;
use DobroSite\Crawler\HTTP\Source\HTTPSource;
use Psr\Http\Message\ResponseInterface;

/**
 * Фабрика документов XML.
 *
 * @since 0.1
 */
class XMLDocumentFactory implements Factory
{
    /**
     * Создаёт документ из ответа HTTP.
     *
     * @param HTTPSource        $source Источник документа.
     * @param string            $uri    URI документа.
     * @param ResponseInterface $response
     *
     * @return Document|null
     *
     * @since 0.1
     */
    public function create(HTTPSource $source, $uri, ResponseInterface $response)
    {
        if (strpos($response->getHeaderLine('content-type'), 'application/xml') === false) {
            return null;
        }

        return new XMLDocument($source, $uri, (string) $response->getBody());
    }
}
