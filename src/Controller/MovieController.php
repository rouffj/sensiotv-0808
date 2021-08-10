<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Omdb\OmdbClient;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MovieController extends AbstractController
{
    /**
     * @Route("/movie/{imdbId}/import", name="movie_import")
     *
     * @param Request $request
     * @return Response
     */
    public function import($imdbId, EntityManagerInterface $entityManager, HttpClientInterface $httpClient): Response
    {
        $omdbClient = new OmdbClient($httpClient, '28c5b7b1', 'https://www.omdbapi.com/');

        $row = $omdbClient->requestById($imdbId);
        $movie = new Movie();
        $movie
            ->setImdbId($row['imdbID'])
            ->setPlot($row['Plot'])
            ->setPoster($row['Poster'])
            ->setReleaseDate(new \DateTime($row['Released']))
            ->setTitle($row['Title'])
        ;

        $entityManager->persist($movie);
        $entityManager->flush();

        return $this->redirectToRoute('movie_show', ['id' => $movie->getId()]);
    }

    /**
     * @Route("/movie/search", name="movie_search")
     *
     * @param Request $request
     * @return Response
     */
    public function search(Request $request, HttpClientInterface $httpClient): Response
    {
        $omdbClient = new OmdbClient($httpClient, '28c5b7b1', 'https://www.omdbapi.com/');

        $keyword = $request->query->get('keyword', 'Harry Potter');
        $rows = $omdbClient->requestBySearch($keyword)['Search'];
        dump($rows);

        return $this->render('movie/search.html.twig', [
            'keyword' => $keyword,
            'movies' => $rows,
        ]);
    }

    /**
     * @Route("/movie/{id}", name="movie_show", requirements={"id": "\d+"}, defaults={"id": 567}, methods={"GET"})
     */
    public function showMovie($id, MovieRepository $movieRepository): Response
    {
        $movie = $movieRepository->findOneBy(['id' => $id]);

        return $this->render('movie/show.html.twig', [
            'movie' => $movie
        ]);
        //return new Response('<h1>page for movie ' . $id . '</h1>');
    }
}
