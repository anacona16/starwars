<?php

namespace App\Controller;

use App\DataNegotiation\FilmsDataNegotiation;
use App\Entity\Film;
use Craue\ConfigBundle\Util\Config;
use Knp\Component\Pager\Event\Subscriber\Paginate\Callback\CallbackPagination;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FilmsController extends AbstractController
{
    /**
     * @Route("/films", name="films")
     */
    public function index(
        Request $request,
        PaginatorInterface $paginator,
        Config $config,
        FilmsDataNegotiation $filmsDataNegotiation
    ): Response {
        // Right now the /films endpoint only contains 6 results, we don't need this here, but we will use the same
        // technique as in the character controller.

        $page = $request->query->getInt('page', 1);
        $requestedPages = $config->get('api_films_requested_pages');

        $items = fn ($offset, $limit) => $filmsDataNegotiation->getAll($limit, $requestedPages, $page);
        $count = fn () => $filmsDataNegotiation->count();

        // We need a "custom" pagination because we are displaying the results from the DB but getting the total results
        // from the API, this will make possible to visit all the results in the API even when they are not in the DB yet.
        $callbackPagination = new CallbackPagination($count, $items);
        $pagination = $paginator->paginate($callbackPagination, $page, 10);

        return $this->render('films/index.html.twig', [
            'films' => $pagination,
        ]);
    }

    /**
     * @Route("/films/{id}", name="films_detail")
     */
    public function detail(Film $film, FilmsDataNegotiation $filmsDataNegotiation) : Response
    {
        $totalCharactersInApi = $filmsDataNegotiation->countCharactersInApi($film);

        // Checking if the character list is empty, if so, we try to get the info from the API.
        if (true === $film->getCharacters()->isEmpty() || $totalCharactersInApi !== $film->getCharacters()->count()) {
            $film = $filmsDataNegotiation->updateCharacters($film);
        }

        return $this->render('films/detail.html.twig', [
            'film' => $film,
        ]);
    }
}
