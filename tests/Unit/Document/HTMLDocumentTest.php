<?php

/**
 * DobroSite HTTP Crawler.
 *
 * @copyright 2019, ООО «Добро.сайт», http://добро.сайт
 */

namespace DobroSite\Crawler\HTTP\Tests\Unit\Document;

use DobroSite\Crawler\HTTP\Document\HTMLDocument;

/**
 * Тесты документа HTML.
 *
 * @covers \DobroSite\Crawler\HTTP\Document\HTMLDocument
 * @covers \DobroSite\Crawler\HTTP\Document\AbstractDocument
 */
class HTMLDocumentTest extends AbstractDocumentTest
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
        $document = new HTMLDocument(
            $source,
            'http://example.com/',
            '<html><body><a href="/foo"></a><a href="/bar"></a></body></html>'
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
        $document = new HTMLDocument($source, 'http://example.com/', '<html></html>');

        self::assertEquals('http://example.com/', $document->uri());
        self::assertEquals('<html></html>', $document->content());
    }
}
