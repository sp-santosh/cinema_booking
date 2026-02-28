<?php
/**
 * ScreeningsController – view screenings & admin scheduling.
 */
class ScreeningsController extends Controller
{
    private Screening $screeningModel;
    private Movie     $movieModel;
    private Hall      $hallModel;
    private Seat      $seatModel;

    public function __construct()
    {
        $this->screeningModel = new Screening();
        $this->movieModel     = new Movie();
        $this->hallModel      = new Hall();
        $this->seatModel      = new Seat();
    }

    // ── Public ──────────────────────────────────────────────────────

    // GET /screenings/{id}
    public function show(string $id): void
    {
        $screening = $this->screeningModel->getDetail((int) $id);
        if (!$screening) {
            http_response_code(404);
            $this->render('errors/404');
            return;
        }

        $seats = $this->seatModel->getAvailability($screening['hall_id'], (int) $id);

        $this->render('screenings/show', [
            'screening' => $screening,
            'seats'     => $seats,
            'pageTitle' => View::e($screening['title']) . ' – ' . date('D j M, H:i', strtotime($screening['start_time'])),
        ]);
    }

    // ── Admin ────────────────────────────────────────────────────────

    // GET /admin/screenings
    public function adminIndex(): void
    {
        Middleware::admin();
        $screenings = $this->screeningModel->getUpcoming(50);
        $this->render('admin/screenings/index', [
            'screenings' => $screenings,
            'pageTitle'  => 'Manage Screenings',
        ]);
    }

    // GET /admin/screenings/create
    public function create(): void
    {
        Middleware::admin();
        $this->render('admin/screenings/form', [
            'pageTitle' => 'Schedule Screening',
            'screening' => null,
            'movies'    => $this->movieModel->findAll('title'),
            'halls'     => $this->hallModel->findAll('name'),
        ]);
    }

    // POST /admin/screenings
    public function store(): void
    {
        Middleware::admin();
        Csrf::verify();

        $v = new Validator($_POST);
        $v->required(['movie_id', 'hall_id', 'start_time', 'end_time'])
          ->numeric('movie_id')
          ->numeric('hall_id');

        if ($v->fails()) {
            $this->render('admin/screenings/form', [
                'errors'    => $v->errors(),
                'old'       => $_POST,
                'screening' => null,
                'movies'    => $this->movieModel->findAll('title'),
                'halls'     => $this->hallModel->findAll('name'),
                'pageTitle' => 'Schedule Screening',
            ]);
            return;
        }

        $this->screeningModel->create($this->allInput());
        $this->flash('success', 'Screening scheduled.');
        $this->redirect(APP_URL . '/admin/screenings');
    }

    // GET /admin/screenings/{id}/edit
    public function edit(string $id): void
    {
        Middleware::admin();
        $screening = $this->screeningModel->findById((int) $id);
        if (!$screening) {
            $this->redirect(APP_URL . '/admin/screenings');
        }

        $this->render('admin/screenings/form', [
            'screening' => $screening,
            'movies'    => $this->movieModel->findAll('title'),
            'halls'     => $this->hallModel->findAll('name'),
            'pageTitle' => 'Edit Screening',
        ]);
    }

    // POST /admin/screenings/{id}
    public function update(string $id): void
    {
        Middleware::admin();
        Csrf::verify();

        $v = new Validator($_POST);
        $v->required(['movie_id', 'hall_id', 'start_time', 'end_time', 'status']);

        if ($v->fails()) {
            $screening = $this->screeningModel->findById((int) $id);
            $this->render('admin/screenings/form', [
                'errors'    => $v->errors(),
                'old'       => $_POST,
                'screening' => $screening,
                'movies'    => $this->movieModel->findAll('title'),
                'halls'     => $this->hallModel->findAll('name'),
                'pageTitle' => 'Edit Screening',
            ]);
            return;
        }

        $this->screeningModel->updateScreening((int) $id, [
            'movie_id'   => (int) $this->input('movie_id'),
            'hall_id'    => (int) $this->input('hall_id'),
            'start_time' => $this->input('start_time'),
            'end_time'   => $this->input('end_time'),
            'status'     => $this->input('status'),
        ]);

        $this->flash('success', 'Screening updated.');
        $this->redirect(APP_URL . '/admin/screenings');
    }

    // POST /admin/screenings/{id}/delete
    public function destroy(string $id): void
    {
        Middleware::admin();
        Csrf::verify();
        $this->screeningModel->delete((int) $id);
        $this->flash('success', 'Screening removed.');
        $this->redirect(APP_URL . '/admin/screenings');
    }
}
