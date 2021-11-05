<?php

namespace App\DataRetriever;

use App\Entity\Film;
use App\Service\BasicClient;
use JsonMapper;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class FilmsDataRetriever
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

    public function all()
    {
        try {
            $response = $this->basicClient->getClient()->request('GET', 'films');

            if (200 === $response->getStatusCode()) {
                $responseJson = json_decode($response->getContent());

                $mapper = new JsonMapper();
                $mapper->classMap[\DateTimeInterface::class] = \DateTime::class;

                return $mapper->mapArray(
                    $responseJson->results, array(), Film::class
                );
            }
        } catch (TransportExceptionInterface $e) {
        }

        return [];
    }
}
