<?php

namespace app\models;

class Recommendation extends Model {

    private $clientId = 'your_spotify_client_id';
    private $clientSecret = 'your_spotify_client_secret';

    /**
     * Fetch music recommendations from Spotify API.
     *
     * @param string $query The search query (artist or vibe).
     * @return array|bool List of recommendations or false on failure.
     */
    public function fetchRecommendations($query) {
        // Get the access token from Spotify
        $accessToken = $this->getSpotifyAccessToken();
        if (!$accessToken) {
            return false; // Failed to get access token
        }

        // Prepare Spotify API search URL
        $apiUrl = "https://api.spotify.com/v1/search";
        $params = [
            'q' => $query,
            'type' => 'artist', // You can also change this to 'track' or 'album' if you want different results
            'limit' => 5 // Limit the number of results
        ];

        $url = $apiUrl . '?' . http_build_query($params);

        // Use cURL to fetch data from Spotify API
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $accessToken"
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        // Handle API response
        if ($response === false) {
            return false; // Request failed
        }

        $recommendations = json_decode($response, true);
        if ($recommendations === null || !isset($recommendations['artists']['items']) || empty($recommendations['artists']['items'])) {
            return false; // No recommendations found
        }

        // Return the artist recommendations
        return $recommendations['artists']['items'];
    }

    /**
     * Get the Spotify Access Token using Client Credentials flow.
     *
     * @return string|null The access token or null if failed.
     */
    private function getSpotifyAccessToken() {
        $url = "https://accounts.spotify.com/api/token";
        $headers = [
            "Authorization: Basic " . base64_encode($this->clientId . ":" . $this->clientSecret)
        ];
        $postFields = "grant_type=client_credentials";

        // Send the request to Spotify to get the access token
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
            return $data['access_token'] ?? null; // Return the access token, or null if not found
        }

        return null; // If the request failed, return null
    }
}