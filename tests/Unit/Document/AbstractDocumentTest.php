<?php

/**
 * DobroSite HTTP Crawler.
 *
 * @copyright 2019, ООО «Добро.сайт», http://добро.сайт
 */

namespace DobroSite\Crawler\HTTP\Tests\Unit\Document;

use DobroSite\Crawler\Source\Source;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Основной класс для тестов документов.
 */
abstract class AbstractDocumentTest extends TestCase
{
    /**
     * Создаёт заглушку источника документов.
     *
     * @param string $rootUri
     *
     * @return Source|MockObject
     *
     * @throws \Exception
     */
    protected function createSource($rootUri)
    {
        $source = $this->createMock(Source::class);

        $source->method('rootUri')->willReturn($rootUri);

        return $source;
    }
}
