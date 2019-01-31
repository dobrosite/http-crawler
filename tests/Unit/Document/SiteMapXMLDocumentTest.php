<?php

/**
 * DobroSite HTTP Crawler.
 *
 * @copyright 2019, ООО «Добро.сайт», http://добро.сайт
 */

namespace DobroSite\Crawler\HTTP\Tests\Unit\Document;

use DobroSite\Crawler\HTTP\Document\SiteMapXMLDocument;

/**
 * Тесты документа sitemap.xml.
 *
 * @covers \DobroSite\Crawler\HTTP\Document\SiteMapXMLDocument
 * @covers \DobroSite\Crawler\HTTP\Document\AbstractDocument
 */
class SiteMapXMLDocumentTest extends AbstractDocumentTest
{
    /**
     * Проверяет извлечение ссылок из документа.
     *
     * @return void
     *
     * @throws \Exception
     */
    public function testAllLinksExtracted()
    {
        $source = $this->createSource('http://example.com/');
        $document = new SiteMapXMLDocument(
            $source,
            'http://example.com/',
            '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.
            '<sitemap><loc>http://example.com/foo</loc></sitemap>'.
            '<sitemap><loc>http://example.com/bar</loc></sitemap>'.
            '</<sitemapindex>'
        );

        self::assertEquals(
            [
                'http://example.com/foo',
                'http://example.com/bar'
            ],
            $document->links()
        );
    }

    /**
     * Проверяет правильность создания документа.
     *
     * @return void
     */
    public function testConstructedProperly()
    {
        $source = $this->createSource('http://example.com/');
        $document = new SiteMapXMLDocument(
            $source,
            'http://example.com/',
            '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></sitemapindex>'
        );

        self::assertEquals('http://example.com/', $document->uri());
        self::assertEquals(
            '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></sitemapindex>',
            $document->content()
        );
    }
}
