<?php

namespace App\DataNegotiation;

use App\DataRetriever\CharactersDataRetriever;
use App\DataRetriever\FilmsDataRetriever;
use App\Entity\Character;
use App\Entity\Film;
use App\Repository\CharacterRepository;
use App\Repository\FilmRepository;
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
     * @var SpecieDataNegotiation
     */
    private $speciesDataNegotiation;

    /**
     * @var FilmsDataRetriever
     */
    private $filmsDataRetriever;

    /**
     * @var FilmRepository
     */
    private $filmRepository;

    /**
     * @param EntityManagerInterface $entityManger
     * @param CharacterRepository $characterRepository
     * @param CharactersDataRetriever $charactersDataRetriever
     * @param SpecieDataNegotiation $speciesDataNegotiation
     * @param FilmsDataRetriever $filmsDataRetriever
     * @param FilmRepository $filmRepository
     */
    public function __construct(
        EntityManagerInterface $entityManger,
        CharacterRepository $characterRepository,
        CharactersDataRetriever $charactersDataRetriever,
        SpecieDataNegotiation $speciesDataNegotiation,
        FilmsDataRetriever $filmsDataRetriever,
        FilmRepository $filmRepository
    ) {
        $this->entityManger = $entityManger;
        $this->characterRepository = $characterRepository;
        $this->charactersDataRetriever = $charactersDataRetriever;
        $this->speciesDataNegotiation = $speciesDataNegotiation;
        $this->filmsDataRetriever = $filmsDataRetriever;
        $this->filmRepository = $filmRepository;
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

        // If the current requested page has not been requested in the API or the results incremented then we make
        // the request to the API, otherwise we get the info from the database.
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
                    'url' => $character->getUrl(),
                ]);

                if (null === $checkCharacter) {
                    $character->setGroupPage($page);
                    $this->entityManger->persist($character);
                } elseif (empty($checkCharacter->getGroupPage())) {
                    $checkCharacter->setGroupPage($page);
                    $this->entityManger->persist($checkCharacter);
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

    /**
     * Checks if the character was already saved into the database,
     * if not, we request it from the API, and then we save it.
     *
     * @param $url
     *
     * @return Character|false|mixed|object
     *
     * @throws \JsonMapper_Exception
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     */
    public function getByUrlAndSave($url)
    {
        $characterInStorage = $this->characterRepository->findOneBy([
            'url' => $url,
        ]);

        if ($characterInStorage instanceof Character) {
            return $characterInStorage;
        }

        $character = $this->charactersDataRetriever->getByUrl($url, true);

        if (false !== $character) {
            $this->entityManger->persist($character);
            $this->entityManger->flush();
        }

        return $character;
    }

    /**
     * Update the Species of the provided film.
     *
     * @param Character $character
     *
     * @return Character
     */
    public function updateFilms(Character $character) : Character
    {
        $characterResponse = $this->charactersDataRetriever->getByUrl($character->getUrl(), false);
        $characterResponseFilms = $characterResponse->films ?? [];

        foreach ($characterResponseFilms as $characterResponse) {
            $filmInStorage = $this->filmRepository->findOneBy([
                'url' => $characterResponse,
            ]);

            if ($filmInStorage instanceof Film) {
                $character->addFilm($filmInStorage);
            } else {
                $filmMapped = $this->filmsDataRetriever->getByUrl($characterResponse);

                if (false !== $filmMapped) {
                    $character->addFilm($filmMapped);
                }
            }
        }

        $this->entityManger->flush();

        return $character;
    }

    /**
     * Update the Species of the provided film.
     *
     * @param Character $character
     *
     * @return Character
     */
    public function updateSpecies(Character $character) : Character
    {
        $characterResponse = $this->charactersDataRetriever->getByUrl($character->getUrl(), false);
        $characterResponseSpecies = $characterResponse->species ?? [];

        foreach ($characterResponseSpecies as $characterResponse) {
            $specieSaved = $this->speciesDataNegotiation->getByUrlAndSave($characterResponse);

            if (false !== $specieSaved) {
                $character->addSpecies($specieSaved);
            }
        }

        $this->entityManger->flush();

        return $character;
    }
}
