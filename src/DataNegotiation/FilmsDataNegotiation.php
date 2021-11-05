<?php

namespace App\DataNegotiation;

use App\DataRetriever\FilmsDataRetriever;
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

    public function getAll()
    {
        $films = $this->filmRepository->findAll();

        if (!empty($films)) {
            return $films;
        }

        $films = $this->filmsDataRetriever->all();

        foreach ($films as $film) {
            $this->entityManger->persist($film);
        }

        $this->entityManger->flush();

        return $films;
    }
}
