<?php

/**
 * DobroSite HTTP Crawler.
 *
 * @copyright 2019, ООО «Добро.сайт», http://добро.сайт
 */

namespace DobroSite\Crawler\HTTP\Document;

use DobroSite\Crawler\Document\Document;
use DobroSite\Crawler\Source\Source;
use GuzzleHttp\Psr7\Uri;

/**
 * Абстрактный документ.
 *
 * @since 0.1
 */
abstract class AbstractDocument implements Document
{
    /**
     * Внешние ссылки из этого документа.
     *
     * @var string[]|null
     */
    private $links;

    /**
     * Источник документа.
     *
     * @var Source
     */
    private $source;

    /**
     * URI документа.
     *
     * @var string
     */
    private $uri;

    /**
     * Создаёт документ.
     *
     * @param Source $source Источник документа.
     * @param string $uri    URI документа.
     *
     * @since 0.1
     */
    public function __construct(Source $source, $uri)
    {
        $this->source = $source;
        $this->uri = $uri;
    }

    /**
     * Возвращает ссылки, содержащиеся в документе.
     *
     * @return string[]
     *
     * @throws \InvalidArgumentException
     *
     * @since 0.1
     */
    public function links()
    {
        if ($this->links === null) {
            $links = $this->findLinks();
            $links = $this->convertToURIs($links);
            $links = $this->filterURIs($links);
            $this->links = array_map(
                function (Uri $uri) {
                    return (string) $uri;
                },
                $links
            );
        }

        return $this->links;
    }

    /**
     * URI документа.
     *
     * @return string
     *
     * @since 0.1
     */
    public function uri()
    {
        return $this->uri;
    }

    /**
     * Преобразует ссылки в URI.
     *
     * @param string[] $links
     *
     * @return Uri[]
     *
     * @throws \InvalidArgumentException
     *
     * @since 0.1
     */
    protected function convertToURIs(array $links)
    {
        $rootUri = new Uri($this->source->rootUri());

        return array_map(
            function ($link) use ($rootUri) {
                $uri = new Uri($link);
                if ($uri->getHost() === '') {
                    $uri = $uri->withHost($rootUri->getHost());
                }

                if ($uri->getScheme() === '') {
                    $uri = $uri->withScheme($rootUri->getScheme());
                }

                return $uri;
            },
            $links
        );
    }

    /**
     * Отфильтровывает лишние URI.
     *
     * @param Uri[] $uris
     *
     * @return Uri[]
     *
     * @throws \InvalidArgumentException
     *
     * @since 0.1
     */
    protected function filterURIs(array $uris)
    {
        $rootUri = new Uri($this->source->rootUri());

        return array_filter(
            $uris,
            function (Uri $uri) use ($rootUri) {
                if (!preg_match('/https?/i', $uri->getScheme())) {
                    return false;
                }

                if (strpos((string) $uri, (string) $rootUri) !== 0) {
                    return false;
                }

                if ($uri->withFragment('') === $this->uri()) {
                    return false;
                }

                return true;
            }
        );
    }

    /**
     * Возвращает все ссылки из документа.
     *
     * @return string[]
     *
     * @since 0.1
     */
    abstract protected function findLinks();
}
