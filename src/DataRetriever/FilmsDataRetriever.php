<?php

namespace App\DataRetriever;

use App\Entity\Film;
use App\Service\BasicClient;
use Craue\ConfigBundle\Util\Config;
use JsonMapper;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class FilmsDataRetriever
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var BasicClient
     */
    private $basicClient;

    /**
     * @param Config $config
     * @param BasicClient $basicClient
     */
    public function __construct(Config $config, BasicClient $basicClient)
    {
        $this->config = $config;
        $this->basicClient = $basicClient;
    }

    /**
     * Makes a request to a specific endpoint and returns the response.
     *
     * @param $page
     *
     * @return \Symfony\Contracts\HttpClient\ResponseInterface
     *
     * @throws TransportExceptionInterface
     */
    public function get($page)
    {
        return $this->basicClient->getClient()->request('GET', 'films', [
            'query' => [
                'page' => $page,
            ],
        ]);
    }

    /**
     * Counts all the results available in the API, we need this for the pagination.
     *
     * @param bool $all
     *
     * @return int|mixed
     *
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     */
    public function count($all = true)
    {
        try {
            $response = $this->get(1);

            if (200 === $response->getStatusCode()) {
                $responseArray = $response->toArray();

                // We can count only the results for a specific page.
                if (false === $all) {
                    return count($responseArray['results']);
                }

                return $responseArray['count'];
            }
        } catch (TransportExceptionInterface $e) {
        }

        return 0;
    }

    /**
     * Returns the characters requested from the API as a Character::class
     * Here we also update the requested pages, this is done to avoid querying the API again.
     *
     * @param $requestedPages
     * @param int $page
     *
     * @return array|mixed
     *
     * @throws \JsonMapper_Exception
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     */
    public function all($requestedPages, $page = 1)
    {
        try {
            $response = $this->get($page);

            if (200 === $response->getStatusCode()) {
                $responseJson = json_decode($response->getContent());
                $responseJsonResults = $responseJson->results;

                $mapper = new JsonMapper();
                $mappedResults = $mapper->mapArray($responseJsonResults, [], Film::class);

                // Updating the requested pages
                $this->config->set('api_films_requested_pages', $requestedPages);

                return $mappedResults;
            }
        } catch (TransportExceptionInterface $e) {
        }

        return [];
    }

    /**
     * Returns the Array response from the API.
     *
     * @param $url
     *
     * @return array
     *
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     */
    public function getByUrl($url)
    {
        try {
            $response = $this->basicClient->getClient()->request('GET', '', [
                'base_uri' => $url,
            ]);

            if (200 === $response->getStatusCode()) {
                return $response->toArray();
            }

        } catch (TransportExceptionInterface $e) {
        }

        return [];
    }
}
