<?php
/**
 * Application Routes
 */

// Public
Router::get('/',                        'HomeController@index');

// Auth
Router::get('/login',                   'AuthController@showLogin');
Router::post('/login',                  'AuthController@login');
Router::get('/register',                'AuthController@showRegister');
Router::post('/register',               'AuthController@register');
Router::post('/logout',                 'AuthController@logout');

// Movies (public)
Router::get('/movies',                  'MoviesController@index');
Router::get('/movies/{id}',             'MoviesController@show');

// Screenings (public)
Router::get('/screenings/{id}',         'ScreeningsController@show');

// Bookings (authenticated)
Router::get('/bookings',                'BookingController@index');
Router::get('/screenings/{id}/book',    'BookingController@create');
Router::post('/bookings',               'BookingController@store');
Router::get('/bookings/{id}/confirmation', 'BookingController@confirmation');
Router::get('/bookings/{id}',           'BookingController@show');
Router::post('/bookings/{id}/cancel',   'BookingController@cancel');

// Payments
Router::post('/payments/checkout',      'PaymentsController@checkout');
Router::post('/payments/webhook',       'PaymentsController@webhook');

// ── Admin ─────────────────────────────────────────────────────────
Router::get('/admin',                           'AdminController@dashboard');
Router::get('/admin/users',                     'AdminController@users');
Router::post('/admin/users/{id}/toggle',        'AdminController@toggleUser');
Router::get('/admin/bookings',                  'AdminController@bookings');
Router::post('/admin/bookings/{id}/cancel',     'AdminController@cancelBooking');

// Admin – Movies
Router::get('/admin/movies',                    'MoviesController@adminIndex');
Router::get('/admin/movies/create',             'MoviesController@create');
Router::post('/admin/movies',                   'MoviesController@store');
Router::get('/admin/movies/{id}/edit',          'MoviesController@edit');
Router::post('/admin/movies/{id}',              'MoviesController@update');
Router::post('/admin/movies/{id}/delete',       'MoviesController@destroy');

// Admin – Screenings
Router::get('/admin/screenings',                'ScreeningsController@adminIndex');
Router::get('/admin/screenings/create',         'ScreeningsController@create');
Router::post('/admin/screenings',               'ScreeningsController@store');
Router::get('/admin/screenings/{id}/edit',      'ScreeningsController@edit');
Router::post('/admin/screenings/{id}',          'ScreeningsController@update');
Router::post('/admin/screenings/{id}/delete',   'ScreeningsController@destroy');
