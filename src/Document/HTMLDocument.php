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
 * Документ HTML.
 *
 * @since 0.1
 */
class HTMLDocument extends AbstractDocument
{
    /**
     * Содержимое документа.
     *
     * @var string
     */
    private $content;

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
        parent::__construct($source, $uri);

        $this->content = $content;
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
     * @throws \RuntimeException
     *
     * @since 0.1
     */
    protected function findLinks()
    {
        $domCrawler = new Crawler($this->content);

        return $domCrawler->filter('a')->each(
            function (Crawler $node) {
                return trim($node->attr('href'));
            }
        );
    }
}
