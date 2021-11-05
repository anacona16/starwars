<?php

namespace App\DataNegotiation;

use App\DataRetriever\FilmsDataRetriever;
use App\Entity\Film;
use App\Repository\FilmRepository;
use Doctrine\ORM\EntityManagerInterface;

class FilmsDataNegotiation
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManger;

    /**
     * @var FilmRepository
     */
    private $filmRepository;

    /**
     * @var FilmsDataRetriever
     */
    private $filmsDataRetriever;

    /**
     * @param EntityManagerInterface $entityManger
     * @param FilmRepository $filmRepository
     * @param FilmsDataRetriever $filmsDataRetriever
     */
    public function __construct(
        EntityManagerInterface $entityManger,
        FilmRepository $filmRepository,
        FilmsDataRetriever $filmsDataRetriever
    ) {
        $this->entityManger = $entityManger;
        $this->filmRepository = $filmRepository;
        $this->filmsDataRetriever = $filmsDataRetriever;
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
        $requestedPages = explode('|', $requestedPages);

        // If the current requested page has not been requested in the API or the results incremented then we make
        // the request to the API, otherwise we get the info from the database.
        if (!in_array($page, $requestedPages)) {
            /**
             * @TODO This could be handle in a better way, but the API does not provide to much info like identifiers.
             */
            $requestedPages[] = $page;
            $requestedPages = array_unique($requestedPages);
            $requestedPages = implode('|', $requestedPages);

            $films = $this->filmsDataRetriever->all($requestedPages, $page);

            /** @var Film $fil */
            foreach ($films as $film) {
                // If the API was requested and the row is not in the database we can save it.
                // This could be better if the API provides an unique ID for each result.
                $checkCharacter = $this->filmRepository->findOneBy([
                    'title' => $film->getTitle(),
                ]);

                if (null === $checkCharacter) {
                    $film->setGroupPage($page);
                    $this->entityManger->persist($film);
                }
            }

            $this->entityManger->flush();
        }

        return $this->filmRepository->findAllPaginated($limit, $page);
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
        return (int) $this->filmsDataRetriever->count($all);
    }
}
