<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\v0\Score;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScoreTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_the_endpoint_returns_successful_creation_with_sorted_response(): void
    {
        // Create scores
        Score::create([
            "name" => "SDO",
            "score" => "100",
        ]);

        Score::create([
            "name" => "DEV",
            "score" => "500",
        ]);

        Score::create([
            "name" => "AAA",
            "score" => "50",
        ]);

        // Set your headers here
        $apiKey = config('app.hoppy_api_key');

        $this->withHeaders([
            'x-api-key' => $apiKey,
            'Accept' => 'application/json'
        ]);

        $body = [
            'name' => 'SDO',
            'score' => 9999
        ];

        $response = $this->post('/api/v0/scores', $body);

        $response->assertStatus(200);

        // Test of ordered correctly
        $this->assertEquals($response->json()["data"][0]["score"], "9999");
        $this->assertEquals($response->json()["data"][1]["score"], "500");
        $this->assertEquals($response->json()["data"][2]["score"], "100");
    }

    public function test_the_endpoint_returns_sorted_response(): void
    {
        // Create scores
        Score::create([
            "name" => "SDO",
            "score" => "100",
        ]);

        Score::create([
            "name" => "DEV",
            "score" => "500",
        ]);

        Score::create([
            "name" => "AAA",
            "score" => "50",
        ]);

        // Set your headers here
        $apiKey = config('app.hoppy_api_key');

        $this->withHeaders([
            'x-api-key' => $apiKey,
            'Accept' => 'application/json'
        ]);

        $response = $this->get('/api/v0/scores');

        $response->assertStatus(200);

        // Test of ordered correctly
        $this->assertEquals($response->json()["data"][0]["name"], "DEV");
        $this->assertEquals($response->json()["data"][1]["name"], "SDO");
        $this->assertEquals($response->json()["data"][2]["name"], "AAA");
    }

    public function test_the_endpoint_returns_403_response_without_api_key(): void
    {
        $response = $this->get('/api/v0/scores');

        $response->assertStatus(403);
    }
}
