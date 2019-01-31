<?php

/**
 * Пример обхода сайта semver.org.
 */

namespace DobroSite\Crawler\HTTP\Examples;

use DobroSite\Crawler\Crawler;
use DobroSite\Crawler\HTTP\Source\HTTPSource;
use DobroSite\Crawler\UriQueue\InMemoryUriQueue;
use Http\Client\Curl\Client;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use Http\Message\StreamFactory\GuzzleStreamFactory;

require dirname(__DIR__).'/vendor/autoload.php';

$httpMessageFactory = new GuzzleMessageFactory();
$httpStreamFactory = new GuzzleStreamFactory();
$httpClient = new Client($httpMessageFactory, $httpStreamFactory);

$rootUri = 'https://semver.org/';

$source = new HTTPSource($rootUri, $httpClient, $httpMessageFactory);
$queue = new InMemoryUriQueue();
$crawler = new Crawler($source, $queue);

foreach ($crawler as $document) {
    $links = $document->links();
    printf("[%4d]: %s (%d)\n", $queue->size(), $document->uri(), count($links));
    foreach ($links as $link) {
        printf("\t%s\n", $link);
    }
}
