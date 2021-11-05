<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class BasicClient
{
    /**
     * @var HttpClientInterface
     */
    private $starwarsClient;

    /**
     * @param HttpClientInterface $starwarsClient
     */
    public function __construct(HttpClientInterface $starwarsClient)
    {
        $this->starwarsClient = $starwarsClient;
    }

    public function getClient()
    {
        return $this->starwarsClient;
    }
}
