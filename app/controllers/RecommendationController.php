<?php

namespace app\controllers;

use app\models\Recommendations;

class RecommendationController extends Controller {
    private $clientId = 'your_spotify_client_id';
    private $clientSecret = 'your_spotify_client_secret';

    /**
     * Render the recommendations page.
     */
    public function recommendationView() {
        $this->returnView('./assets/views/recommendations/recommendation.html');
    }

    /**
     * Fetch artist recommendations from Spotify API.
     *
     * @param string $query The vibe or artist name to search.
     */
    public function getRecommendations($query) {
        // Validate the input
        $query = htmlspecialchars(trim($query), ENT_QUOTES, 'UTF-8');
        if (empty($query)) {
            $this->returnJSON([
                'error' => true,
                'message' => 'Search query is required.'
            ]);
            return;
        }

        try {
            // Get Spotify Access Token
            $accessToken = $this->getSpotifyAccessToken();
            if (!$accessToken) {
                throw new \Exception('Unable to authenticate with Spotify API.');
            }

            // Prepare API URL and parameters
            $apiUrl = "https://api.spotify.com/v1/search";
            $params = [
                'q' => $query,
                'type' => 'artist',
                'limit' => 5
            ];

            $url = $apiUrl . '?' . http_build_query($params);

            // Fetch data from Spotify API
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer $accessToken"
            ]);

            $response = curl_exec($ch);
            curl_close($ch);

            if ($response === false) {
                throw new \Exception('Unable to fetch data from Spotify API.');
            }

            $recommendations = json_decode($response, true);
            if ($recommendations === null) {
                throw new \Exception('Failed to decode Spotify API response: ' . json_last_error_msg());
            }

            // Check if the response contains results
            if (isset($recommendations['artists']['items']) && !empty($recommendations['artists']['items'])) {
                $this->returnJSON([
                    'error' => false,
                    'data' => $recommendations['artists']['items']
                ]);
            } else {
                $this->returnJSON([
                    'error' => true,
                    'message' => 'No recommendations found for the provided query.'
                ]);
            }
        } catch (\Exception $e) {
            $this->returnJSON([
                'error' => true,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get Spotify API Access Token using Client Credentials Flow.
     *
     * @return string|null The access token or null on failure.
     */
    private function getSpotifyAccessToken() {
        $url = "https://accounts.spotify.com/api/token";
        $headers = [
            "Authorization: Basic " . base64_encode($this->clientId . ":" . $this->clientSecret)
        ];
        $postFields = "grant_type=client_credentials";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            $data = json_decode($response, true);
            return $data['access_token'] ?? null;
        }

        return null;
    }
}