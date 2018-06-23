<?php

declare(strict_types=1);

namespace App\Factory;

use GuzzleHttp\Client;
use GuzzleHttp\Command\CommandInterface;
use GuzzleHttp\Command\Guzzle\Deserializer;
use GuzzleHttp\Command\Result;
use HackerNewsApi\Client\HackerNewsClient;
use HackerNewsApi\Client\HackerNewsClientInterface;
use HackerNewsApi\Service\HackerNewsApiDescription;
use HackerNewsApi\Service\HackerNewsServiceClient;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Modified \HackerNewsApi\Service\HackerNewsServiceClient::create().
 */
class HackerNewsClientFactory
{
    public function create(
        Client $client
    ): HackerNewsClientInterface {
        $description = HackerNewsApiDescription::get();

        $nullToEmptyResult = function (
            ResponseInterface $response,
            RequestInterface $request,
            CommandInterface $command
        ) use ($description) {
            // The HN API returns status code 200 when the item isn't found,
            // with "null" in the body. This causes the deserializer to bork,
            // emitting a PHP warning (Invalid argument supplied for foreach).
            // We'll intercept this here, and just return an empty Result
            // ourselves.
            if ('null' === $response->getBody()->getContents()) {
                return new Result();
            }

            $handler = new Deserializer($description, true);

            return $handler($response, $request, $command);
        };

        $guzzleCommand = new HackerNewsServiceClient($client, $description, null, $nullToEmptyResult);

        return new HackerNewsClient(
            $guzzleCommand
        );
    }
}
