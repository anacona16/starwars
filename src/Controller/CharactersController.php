<?php

namespace App\Controller;

use App\DataNegotiation\CharactersDataNegotiation;
use App\Entity\Character;
use Craue\ConfigBundle\Util\Config;
use Knp\Component\Pager\Event\Subscriber\Paginate\Callback\CallbackPagination;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CharactersController extends AbstractController
{
    /**
     * @Route("/characters", name="characters")
     */
    public function index(
        Request $request,
        PaginatorInterface $paginator,
        Config $config,
        CharactersDataNegotiation $charactersDataNegotiation
    ): Response {
        $page = $request->query->getInt('page', 1);
        $requestedPages = $config->get('api_characters_requested_pages');

        $items = fn ($offset, $limit) => $charactersDataNegotiation->getAll($limit, $requestedPages, $page);
        $count = fn () => $charactersDataNegotiation->count();

        // We need a "custom" pagination because we are displaying the results from the DB but getting the total results
        // from the API, this will make possible to visits al the results in the API even when they are not in the DB yet.
        $callbackPagination = new CallbackPagination($count, $items);
        $pagination = $paginator->paginate($callbackPagination, $page, 10);

        return $this->render('characters/index.html.twig', [
            'characters' => $pagination,
        ]);
    }

    /**
     * @Route("/characters/{id}", name="characters_detail")
     */
    public function detail(Character $character, CharactersDataNegotiation $charactersDataNegotiation)
    {
        return $this->render('characters/detail.html.twig', [
            'character' => $character,
        ]);
    }
}
