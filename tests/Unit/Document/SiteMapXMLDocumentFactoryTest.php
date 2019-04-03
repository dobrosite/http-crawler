<?php

/**
 * DobroSite HTTP Crawler.
 *
 * @copyright 2019, ООО «Добро.сайт», http://добро.сайт
 */

namespace DobroSite\Crawler\HTTP\Tests\Unit\Document;

use DobroSite\Crawler\HTTP\Document\SiteMapXMLDocumentFactory;
use DobroSite\Crawler\HTTP\Source\HTTPSource;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Тесты фабрики документов sitemap.xml.
 *
 * @@covers \DobroSite\Crawler\HTTP\Document\SiteMapXMLDocumentFactory
 */
class SiteMapXMLDocumentFactoryTest extends TestCase
{
    /**
     * Поставщик карт в разных форматах.
     *
     * @return array
     */
    public function differentFormatsProvider()
    {
        return [
            [
                $this->createResponse(
                    '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></sitemapindex>',
                    ['content-type' => 'application/xml']
                )
            ],
            [
                $this->createResponse(
                    '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>',
                    ['content-type' => 'text/xml;charset=windows-1251']
                )
            ]
        ];
    }

    /**
     * @param ResponseInterface $response
     *
     * @dataProvider differentFormatsProvider
     */
    public function testDifferentFormats(ResponseInterface $response)
    {
        $factory = new SiteMapXMLDocumentFactory();

        $source = $this->getMockBuilder(HTTPSource::class)->disableOriginalConstructor()->getMock();

        $document = $factory->create($source, 'http://example.com', $response);

        self::assertNotNull($document);
    }

    /**
     * Создаёт ответ HTTP.
     *
     * @param string $body
     * @param array  $headers
     *
     * @return ResponseInterface|MockObject
     */
    private function createResponse($body, array $headers)
    {
        $stream = $this->getMockBuilder(StreamInterface::class)->getMock();
        $stream->method('__toString')->willReturn($body);

        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $response->method('getBody')->willReturn($stream);

        $map = [];
        foreach ($headers as $header => $value) {
            $map[] = [$header, $value];
        }

        $response->method('getHeaderLine')->willReturnMap($map);

        return $response;
    }
}
