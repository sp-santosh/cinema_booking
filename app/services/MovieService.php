<?php
/**
 * Manages movies and coordinates with IMDB API to fetch automatic ratings/data.
 */
class MovieService
{
    private Movie $movieModel;
    private ImdbGatewayService $imdbService;

    public function __construct()
    {
        $this->movieModel  = new Movie();
        $this->imdbService = new ImdbGatewayService();
    }

    public function createMovieWithDetails(array $data): int
    {
        // Attempt to fetch details from IMDB to auto-fill description or rating
        if (empty($data['description']) || empty($data['movie_rating'])) {
            $imdbData = $this->imdbService->fetchMovieDetails($data['title']);
            
            if ($imdbData) {
                if (empty($data['description'])) {
                    $data['description'] = $imdbData['Plot'] ?? null;
                }
                if (empty($data['movie_rating']) && isset($imdbData['imdbRating']) && $imdbData['imdbRating'] !== 'N/A') {
                    $data['movie_rating'] = (float) $imdbData['imdbRating'];
                }
                if (empty($data['poster_url']) && isset($imdbData['Poster']) && $imdbData['Poster'] !== 'N/A') {
                    $data['poster_url'] = $imdbData['Poster'];
                }
            }
        }

        return $this->movieModel->create($data);
    }
}
