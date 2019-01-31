<?php

/**
 * DobroSite HTTP Crawler.
 *
 * @copyright 2019, ООО «Добро.сайт», http://добро.сайт
 */

namespace DobroSite\Crawler\HTTP\Document;

use DobroSite\Crawler\Source\Source;

/**
 * Документ sitemap.xml.
 *
 * @since 0.1
 */
class SiteMapXMLDocument extends XMLDocument
{
    /**
     * Создаёт документ.
     *
     * @param Source $source  Источник документа.
     * @param string $uri     URI документа.
     * @param string $content Содержимое документа.
     *
     * @since 0.1
     */
    public function __construct(Source $source, $uri, $content)
    {
        parent::__construct($source, $uri, $content, '//loc');
    }
}
