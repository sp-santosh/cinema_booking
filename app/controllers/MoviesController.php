<?php
/**
 * MoviesController – public movie browsing & admin movie management.
 */
class MoviesController extends Controller
{
    private Movie     $movieModel;
    private Screening $screeningModel;

    public function __construct()
    {
        $this->movieModel     = new Movie();
        $this->screeningModel = new Screening();
    }

    // ── Public ──────────────────────────────────────────────────────

    // GET /movies
    public function index(): void
    {
        $page    = max(1, (int) $this->query('page', 1));
        $search  = $this->query('search', '');
        $movies  = $search
            ? $this->movieModel->search($search)
            : $this->movieModel->getActive($page);
        $total   = $this->movieModel->countActive();
        $pages   = (int) ceil($total / ITEMS_PER_PAGE);

        $this->render('movies/index', [
            'movies'    => $movies,
            'page'      => $page,
            'pages'     => $pages,
            'search'    => $search,
            'pageTitle' => 'Now Showing',
        ]);
    }

    // GET /movies/{id}
    public function show(string $id): void
    {
        $movie = $this->movieModel->findById((int) $id);
        if (!$movie) {
            http_response_code(404);
            $this->render('errors/404');
            return;
        }

        $screenings = $this->screeningModel->getUpcomingByMovie((int) $id);

        $this->render('movies/show', [
            'movie'      => $movie,
            'screenings' => $screenings,
            'pageTitle'  => View::e($movie['title']),
        ]);
    }

    // ── Admin CRUD ───────────────────────────────────────────────────

    // GET /admin/movies
    public function adminIndex(): void
    {
        Middleware::admin();
        $page   = max(1, (int) $this->query('page', 1));
        $movies = $this->movieModel->getActive($page);
        $total  = $this->movieModel->countActive();
        $pages  = (int) ceil($total / ITEMS_PER_PAGE);

        $this->render('admin/movies/index', [
            'movies'    => $movies,
            'page'      => $page,
            'pages'     => $pages,
            'pageTitle' => 'Manage Movies',
        ]);
    }

    // GET /admin/movies/create
    public function create(): void
    {
        Middleware::admin();
        $this->render('admin/movies/form', ['pageTitle' => 'Add Movie', 'movie' => null]);
    }

    // POST /admin/movies
    public function store(): void
    {
        Middleware::admin();
        Csrf::verify();

        $v = new Validator($_POST);
        $v->required(['title', 'duration_minutes', 'age_rating'])
          ->numeric('duration_minutes')
          ->numeric('movie_rating');

        if ($v->fails()) {
            $this->render('admin/movies/form', [
                'errors'    => $v->errors(),
                'old'       => $_POST,
                'pageTitle' => 'Add Movie',
                'movie'     => null,
            ]);
            return;
        }

        $data = $this->allInput();
        $data['poster_url'] = $this->handlePosterUpload() ?: ($this->input('poster_url') ?: null);

        $this->movieModel->create($data);
        $this->flash('success', 'Movie added successfully.');
        $this->redirect(APP_URL . '/admin/movies');
    }

    // GET /admin/movies/{id}/edit
    public function edit(string $id): void
    {
        Middleware::admin();
        $movie = $this->movieModel->findById((int) $id);
        if (!$movie) {
            $this->redirect(APP_URL . '/admin/movies');
        }

        $this->render('admin/movies/form', ['movie' => $movie, 'pageTitle' => 'Edit Movie']);
    }

    // POST /admin/movies/{id}  (form uses _method=PUT)
    public function update(string $id): void
    {
        Middleware::admin();
        Csrf::verify();

        $v = new Validator($_POST);
        $v->required(['title', 'duration_minutes', 'age_rating'])
          ->numeric('duration_minutes');

        if ($v->fails()) {
            $movie = $this->movieModel->findById((int) $id);
            $this->render('admin/movies/form', [
                'errors'    => $v->errors(),
                'old'       => $_POST,
                'movie'     => $movie,
                'pageTitle' => 'Edit Movie',
            ]);
            return;
        }

        // Determine poster: uploaded file > new URL > keep existing
        $uploadedPath = $this->handlePosterUpload();
        $posterUrl    = $this->input('poster_url');
        $existing     = $this->movieModel->findById((int) $id);

        if ($uploadedPath) {
            $finalPoster = $uploadedPath;
        } elseif ($posterUrl) {
            $finalPoster = $posterUrl;
        } else {
            $finalPoster = $existing['poster_url'] ?? null;
        }

        $this->movieModel->updateMovie((int) $id, [
            'title'            => $this->input('title'),
            'duration_minutes' => (int) $this->input('duration_minutes'),
            'age_rating'       => $this->input('age_rating'),
            'movie_rating'     => $this->input('movie_rating') ?: null,
            'description'      => $this->input('description'),
            'poster_url'       => $finalPoster,
        ]);

        $this->flash('success', 'Movie updated.');
        $this->redirect(APP_URL . '/admin/movies');
    }

    // POST /admin/movies/{id}/delete  (form uses _method=DELETE)
    public function destroy(string $id): void
    {
        Middleware::admin();
        Csrf::verify();
        $this->movieModel->delete((int) $id);
        $this->flash('success', 'Movie removed.');
        $this->redirect(APP_URL . '/admin/movies');
    }

    // ── Helpers ─────────────────────────────────────────────────────

    /**
     * Process an uploaded poster file.
     * Returns the relative URL path (e.g. /assets/images/posters/abc123.png) or null.
     */
    private function handlePosterUpload(): ?string
    {
        if (empty($_FILES['poster_file']['tmp_name']) || $_FILES['poster_file']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $file = $_FILES['poster_file'];

        // Validate type
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $finfo   = new \finfo(FILEINFO_MIME_TYPE);
        $mime    = $finfo->file($file['tmp_name']);
        if (!in_array($mime, $allowed)) {
            return null;
        }

        // Validate size (max 5 MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            return null;
        }

        // Generate unique filename
        $ext  = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif',
            default      => 'jpg',
        };
        $name = uniqid('poster_', true) . '.' . $ext;
        $dest = PUBLIC_PATH . '/assets/images/posters/' . $name;

        // Ensure directory exists
        if (!is_dir(dirname($dest))) {
            mkdir(dirname($dest), 0755, true);
        }

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            return null;
        }

        return '/assets/images/posters/' . $name;
    }
}
