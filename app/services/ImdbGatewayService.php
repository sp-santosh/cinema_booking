<?php
/**
 * Wrapper for OMDB/IMDB API.
 */
class ImdbGatewayService
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $config = require APP_PATH . '/config/imdb.php';
        $this->apiKey  = $config['api_key'];
        $this->baseUrl = $config['base_url'];
    }

    public function fetchMovieDetails(string $title): ?array
    {
        if ($this->apiKey === 'REPLACE_ME') {
            return null; // Mock fallback
        }

        $url = $this->baseUrl . '?t=' . urlencode($title) . '&apikey=' . $this->apiKey;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        if (!$response) return null;

        $data = json_decode($response, true);
        
        if (isset($data['Response']) && $data['Response'] === 'True') {
            return $data;
        }

        return null;
    }
}
