<?php

/**
 * DobroSite HTTP Crawler.
 *
 * @copyright 2019, ООО «Добро.сайт», http://добро.сайт
 */

namespace DobroSite\Crawler\HTTP\Tests\Unit\Document;

use DobroSite\Crawler\Source\Source;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

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
     */
    protected function createSource($rootUri)
    {
        $source = $this->getMockForAbstractClass(Source::class);

        $source->method('rootUri')->willReturn($rootUri);

        return $source;
    }
}
