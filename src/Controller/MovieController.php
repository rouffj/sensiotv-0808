<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
    /**
     * @Route("/movie/search", name="movie_search")
     *
     * @param Request $request
     * @return Response
     */
    public function search(Request $request): Response
    {
        $keyword = $request->query->get('keyword', 'Harry Potter');

        return $this->render('movie/search.html.twig', [
            'keyword' => $keyword
        ]);
    }

    /**
     * @Route("/movie/{id}", name="movie_show", requirements={"id": "\d+"}, defaults={"id": 567}, methods={"GET"})
     */
    public function showMovie($id): Response
    {
        return $this->render('movie/show.html.twig');
        //return new Response('<h1>page for movie ' . $id . '</h1>');
    }
}
