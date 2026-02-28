<?php
/**
 * HomeController – landing page & movie catalogue.
 */
class HomeController extends Controller
{
    private Movie     $movieModel;
    private Screening $screeningModel;

    public function __construct()
    {
        $this->movieModel     = new Movie();
        $this->screeningModel = new Screening();
    }

    // GET /
    public function index(): void
    {
        $upcomingScreenings = $this->screeningModel->getUpcoming(12);
        $featuredMovies     = $this->movieModel->getActive(1, 6);

        $this->render('movies/index', [
            'upcomingScreenings' => $upcomingScreenings,
            'featuredMovies'     => $featuredMovies,
            'pageTitle'          => 'CineBook – Book Your Cinema Tickets',
        ]);
    }
}
