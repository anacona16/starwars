<?php

namespace App\DataNegotiation;

use App\DataRetriever\CharactersDataRetriever;
use App\Entity\Character;
use App\Repository\CharacterRepository;
use Doctrine\ORM\EntityManagerInterface;

class CharactersDataNegotiation
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManger;

    /**
     * @var CharacterRepository
     */
    private $characterRepository;

    /**
     * @var CharactersDataRetriever
     */
    private $charactersDataRetriever;

    /**
     * @param EntityManagerInterface $entityManger
     * @param CharacterRepository $characterRepository
     * @param CharactersDataRetriever $charactersDataRetriever
     */
    public function __construct(
        EntityManagerInterface $entityManger,
        CharacterRepository $characterRepository,
        CharactersDataRetriever $charactersDataRetriever
    ) {
        $this->entityManger = $entityManger;
        $this->characterRepository = $characterRepository;
        $this->charactersDataRetriever = $charactersDataRetriever;
    }

    /**
     * Checks if the info was already requested to the API, if not we make a request, otherwise we get the info from DB.
     *
     * @param $limit
     * @param int $requestedPages
     * @param int $page
     *
     * @return int|mixed|string
     *
     * @throws \JsonMapper_Exception
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     */
    public function getAll($limit, $requestedPages = 0, $page = 1)
    {
        $countCharactersAPI = $this->count(false);
        $requestedPages = explode('|', $requestedPages);

        // If the current requested pages has not been requested in the API or the results incremented then we make
        // the request to the API, otherwise we get the info from database.
        if (!in_array($page, $requestedPages) || $countCharactersAPI < 10) {
            /**
             * @TODO This could be handle in a better way, but the API does not provide to much info like identifiers.
             */
            $requestedPages[] = $page;
            $requestedPages = array_unique($requestedPages);
            $requestedPages = implode('|', $requestedPages);

            $characters = $this->charactersDataRetriever->all($requestedPages, $page);

            /** @var Character $character */
            foreach ($characters as $character) {
                // If the API was requested and the row is not in the database we can save it.
                // This could be better if the API provides an unique ID for each result.
                $checkCharacter = $this->characterRepository->findOneBy([
                    'name' => $character->getName(),
                ]);

                if (null === $checkCharacter) {
                    $character->setGroupPage($page);
                    $this->entityManger->persist($character);
                }
            }

            $this->entityManger->flush();
        }

        return $this->characterRepository->findAllPaginated($limit, $page);
    }

    /**
     * This is a wrapper to count all the results or results per page.
     *
     * @param bool $all
     *
     * @return int
     *
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     */
    public function count($all = true)
    {
        return (int) $this->charactersDataRetriever->count($all);
    }
}