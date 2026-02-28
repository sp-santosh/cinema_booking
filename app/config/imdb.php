<?php
/**
 * OMDB / IMDB API configuration.
 *
 * Register for a free key at https://www.omdbapi.com/apikey.aspx
 */

return [
    'api_key'  => getenv('IMDB_API_KEY') ?: 'REPLACE_ME',
    'base_url' => 'https://www.omdbapi.com/',
];
