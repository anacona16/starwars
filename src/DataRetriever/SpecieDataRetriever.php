<?php

namespace App\DataRetriever;

use App\Entity\Specie;
use App\Service\BasicClient;
use JsonMapper;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class SpecieDataRetriever
{
    /**
     * @var BasicClient
     */
    private $basicClient;

    /**
     * @param BasicClient $basicClient
     */
    public function __construct(BasicClient $basicClient)
    {
        $this->basicClient = $basicClient;
    }

    /**
     * Get the character from the API and map it in a Character class.
     *
     * @param $url
     *
     * @return false|mixed|object
     *
     * @throws \JsonMapper_Exception
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     */
    public function getByUrl($url, $mapped = true)
    {
        try {
            $response = $this->basicClient->getClient()->request('GET', '', [
                'base_uri' => $url,
            ]);

            if (200 === $response->getStatusCode()) {
                $responseJson = json_decode($response->getContent());

                if (false === $mapped) {
                    return $responseJson;
                }

                $mapper = new JsonMapper();

                return $mapper->map($responseJson, new Specie());
            }
        } catch (TransportExceptionInterface $e) {
        }

        return false;
    }
}
