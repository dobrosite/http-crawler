<?php

/**
 * DobroSite HTTP Crawler.
 *
 * @copyright 2019, ООО «Добро.сайт», http://добро.сайт
 */

namespace DobroSite\Crawler\HTTP\Document;

use DobroSite\Crawler\Source\Source;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Документ XML.
 *
 * @since 0.1
 */
class XMLDocument extends AbstractDocument
{
    /**
     * Содержимое документа.
     *
     * @var string
     */
    private $content;

    /**
     * Выражение XPath для поиска ссылок.
     *
     * @var string
     */
    private $linkXPath;

    /**
     * Создаёт документ.
     *
     * @param Source $source    Источник документа.
     * @param string $uri       URI документа.
     * @param string $content   Содержимое документа.
     * @param string $linkXPath Выражение XPath для поиска ссылок.
     *
     * @since 0.1
     */
    public function __construct(Source $source, $uri, $content, $linkXPath)
    {
        parent::__construct($source, $uri);

        $this->content = $content;
        $this->linkXPath = $linkXPath;
    }

    /**
     * Тело документа.
     *
     * @return string
     *
     * @since 0.1
     */
    public function content()
    {
        return $this->content;
    }

    /**
     * Возвращает все ссылки из документа.
     *
     * @return string[]
     *
     * @since 0.1
     */
    protected function findLinks()
    {
        $domCrawler = new Crawler($this->content);

        return $domCrawler->filterXPath($this->linkXPath)->each(
            function (Crawler $node) {
                return $node->text();
            }
        );
    }
}
