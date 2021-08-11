<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\Review;
use App\Form\ReviewType;
use App\Omdb\OmdbClient;
use App\Repository\MovieRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{

    /**
     * @var OmdbClient
     */
    private $omdbClient;

    public function __construct(OmdbClient $omdbClient)
    {
        $this->omdbClient = $omdbClient;
    }

    /**
     * @Route("/movie/{imdbId}/import", name="movie_import")
     *
     * @param Request $request
     * @return Response
     */
    public function import($imdbId, EntityManagerInterface $entityManager): Response
    {
        $row = $this->omdbClient->requestById($imdbId);
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
    public function search(Request $request): Response
    {
        $keyword = $request->query->get('keyword', 'Harry Potter');
        $rows = $this->omdbClient->requestBySearch($keyword)['Search'];
        dump($rows);

        return $this->render('movie/search.html.twig', [
            'keyword' => $keyword,
            'movies' => $rows,
        ]);
    }

    /**
     * @Route("/movie/{id}", name="movie_show", requirements={"id": "\d+"}, defaults={"id": 567}, methods={"GET", "POST"})
     */
    public function showMovie($id,
                              EntityManagerInterface $entityManager,
                              MovieRepository $movieRepository,
                              UserRepository $userRepository,
                              Request $request): Response
    {
        $movie = $movieRepository->findOneBy(['id' => $id]);

        $form = $this->createForm(ReviewType::class);

        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $reviewInfo = $form->getData();
            $user = $userRepository->findOneBy(['email' => $reviewInfo['email']]);
            $review = new Review();
            $review
                ->setBody($reviewInfo['body'])
                ->setRating($reviewInfo['rating'])
                ->setMovie($movie)
                ->setUser($user)
            ;

            $entityManager->persist($review);
            $entityManager->flush();
        }

        return $this->render('movie/show.html.twig', [
            'movie' => $movie,
            'review_form' => $form->createView(),
        ]);
        //return new Response('<h1>page for movie ' . $id . '</h1>');
    }

    /**
     * @Route("/movie/latest", name="movie_latest")
     *
     * @param Request $request
     * @return Response
     */
    public function latest(MovieRepository $movieRepository): Response
    {
        $movies = $movieRepository->findAll();

        return $this->render('movie/latest.html.twig', [
            'movies' => $movies
        ]);
    }
}
