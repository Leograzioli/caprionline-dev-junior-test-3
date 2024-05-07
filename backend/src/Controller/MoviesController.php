<?php

namespace App\Controller;

use App\Repository\GenreRepository;
use App\Repository\MovieGenreRepository;
use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class MoviesController extends AbstractController
{
    public function __construct(
        private MovieRepository $movieRepository,
        private GenreRepository $genreRepository,
        private SerializerInterface $serializer
    ) {
    }

    #[Route('/movies', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $recent = $request->query->get('recent');
        $rating = $request->query->get('rating');
        $genre = $request->query->get('genre');

        if ($recent) {
            
            $movies = $this->movieRepository->findBy([], ["year" => $recent]);
            $data = $this->serializer->serialize($movies, "json", ["groups" => "default"]);
            return new JsonResponse($data, json: true);
        }

        if ($rating) {
            
            $movies = $this->movieRepository->findBy([], ["rating" => $rating]);
            $data = $this->serializer->serialize($movies, "json", ["groups" => "default"]);
            return new JsonResponse($data, json: true);
        }

        if ($genre) {
            
            $movies = $this->movieRepository->findBy([], ["genre" => $genre]);
            $data = $this->serializer->serialize($movies, "json", ["groups" => "default"]);
            return new JsonResponse($data, json: true);
        }

        $movies = $this->movieRepository->findAll();
        $data = $this->serializer->serialize($movies, "json", ["groups" => "default"]);

        return new JsonResponse($data, json: true);
    }

    #[Route('/genres', methods: ['GET'])]
    public function genres(): JsonResponse
    {
        $genres = $this->genreRepository->findAll();
        $data = []; // Initialize an empty array
    
        foreach ($genres as $genre) {
            // Add each genre to the data array
            $data[] = [
                'id' => $genre->getId(),
                'name' => $genre->getName(),
                // Add other properties as needed
            ];
        }
    
        return new JsonResponse($data);
    }

    #[Route('/movies/genre/{genre}', methods: ['GET'])]
    public function listWithFilter($genre, MovieGenreRepository $MovieRepository): jsonResponse
    {

        $movies = $MovieRepository->findBy(['genre' => $genre]);
        $moviesArray = [];

        foreach ($movies as $movieAndGenre) {
            $movie = $movieAndGenre->getMovie();


            // Construct array representing movie data
            $movieData = [
                'id' => $movie->getId(),
                'title' => $movie->getTitle(),
                'imageUrl' => $movie->getImageUrl(),
                'plot' => $movie->getPlot(),
                'year' => $movie->getYear(),
                'releaseDate' => $movie->getReleaseDate(),
                'duration' => $movie->getDuration(),
                'rating' => $movie->getRating(),
                'wikipediaUrl' => $movie->getWikipediaUrl(),
            ];

            // Add this movie data to the array of movies
            $moviesArray[] = $movieData;
        }
        $data = $this->serializer->serialize($moviesArray, "json", ["groups" => "default"]);


        return new JsonResponse($data, json: true);
    }
}
