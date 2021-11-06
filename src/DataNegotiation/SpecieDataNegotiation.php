<?php

namespace App\DataNegotiation;

use App\DataRetriever\SpecieDataRetriever;
use App\Entity\Character;
use App\Entity\Specie;
use App\Repository\SpecieRepository;
use Doctrine\ORM\EntityManagerInterface;

class SpecieDataNegotiation
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManger;

    /**
     * @var SpecieRepository
     */
    private $specieRepository;

    /**
     * @var SpecieDataRetriever
     */
    private $specieDataRetriever;

    /**
     * @param EntityManagerInterface $entityManger
     * @param SpecieRepository $specieRepository
     * @param SpecieDataRetriever $specieDataRetriever
     */
    public function __construct(
        EntityManagerInterface $entityManger,
        SpecieRepository $specieRepository,
        SpecieDataRetriever $specieDataRetriever
    ) {
        $this->entityManger = $entityManger;
        $this->specieRepository = $specieRepository;
        $this->specieDataRetriever = $specieDataRetriever;
    }

    /**
     * Checks if the Specie was already saved into the database,
     * if not, we request it from the API, and then we save it.
     *
     * @param $url
     *
     * @return Specie|false|mixed|object
     *
     * @throws \JsonMapper_Exception
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     */
    public function getByUrlAndSave($url)
    {
        $specieInStorage = $this->specieRepository->findOneBy([
            'url' => $url,
        ]);

        if ($specieInStorage instanceof Specie) {
            return $specieInStorage;
        }

        $specie = $this->specieDataRetriever->getByUrl($url);

        if (false !== $specie) {
            $this->entityManger->persist($specie);
            $this->entityManger->flush();
        }

        return $specie;
    }
}
