<?php

/**
 * DobroSite HTTP Crawler.
 *
 * @copyright 2019, ООО «Добро.сайт», http://добро.сайт
 */

namespace DobroSite\Crawler\HTTP\Tests\Unit\Document;

use DobroSite\Crawler\HTTP\Document\XMLDocument;

/**
 * Тесты документа XML.
 *
 * @covers \DobroSite\Crawler\HTTP\Document\XMLDocument
 * @covers \DobroSite\Crawler\HTTP\Document\AbstractDocument
 */
class XMLDocumentTest extends AbstractDocumentTest
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
        $document = new XMLDocument(
            $source,
            'http://example.com/',
            '<foo><link uri="/foo"/><link uri="/bar"/></foo>',
            '//link/@uri'
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
        $document = new XMLDocument($source, 'http://example.com/', '<content/>', 'xpath');

        self::assertEquals('http://example.com/', $document->uri());
        self::assertEquals('<content/>', $document->content());
    }
}
