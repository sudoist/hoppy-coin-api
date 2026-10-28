<?php

namespace Tests\Feature\Endpoints\v1;

use App\Models\v1\Score;
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
            "level" => "Ranked1",
            "origin" => "phpunit",
            "version" => "phpunit",
        ]);

        Score::create([
            "name" => "DEV",
            "score" => "500",
            "level" => "Ranked2",
            "origin" => "phpunit",
            "version" => "phpunit",
        ]);

        Score::create([
            "name" => "AAA",
            "score" => "50",
            "level" => "Ranked3",
            "origin" => "phpunit",
            "version" => "phpunit",
        ]);

        // Set your headers here
        $apiKey = config('app.hoppy_api_key');

        $this->withHeaders([
            'x-api-key' => $apiKey,
            'Accept' => 'application/json',
            'Origin' => 'composer'
        ]);

        $body = [
            'name' => 'SDO',
            'score' => "9999",
            'level' => "Endpoint"
        ];

        $response = $this->post('/api/v1/scores', $body);

        $response->assertStatus(200);

        // Test of ordered correctly
        $this->assertEquals($response->json()["data"][0]["score"], "9999");
        $this->assertEquals($response->json()["data"][0]["level"], "Endpoint");
        $this->assertEquals($response->json()["data"][0]["version"], "composer");
        $this->assertCount(1, $response->json("data"));
    }

    public function test_the_endpoint_returns_sorted_response(): void
    {
        // Create scores
        Score::create([
            "name" => "SDO",
            "score" => "100",
            "level" => "Ranked1",
            "origin" => "phpunit",
            "version" => "0.1.1.0",
        ]);

        Score::create([
            "name" => "DEV",
            "score" => "500",
            "level" => "Ranked2",
            "origin" => "phpunit",
            "version" => "phpunit",
        ]);

        Score::create([
            "name" => "AAA",
            "score" => "50",
            "level" => "Ranked3",
            "origin" => "phpunit",
            "version" => "phpunit",
        ]);

        // Set your headers here
        $apiKey = config('app.hoppy_api_key');

        $this->withHeaders([
            'x-api-key' => $apiKey,
            'Accept' => 'application/json'
        ]);

        $response = $this->get('/api/v1/scores?level=Ranked1');

        $response->assertStatus(200);

        // Test of ordered correctly
        $this->assertCount(1, $response->json("data"));
        $this->assertEquals($response->json("data.0.name"), "SDO");
        $this->assertEquals($response->json("data.0.version"), "0.1.1.0");
    }

    public function test_the_endpoint_returns_403_response_without_api_key(): void
    {
        $response = $this->get('/api/v1/scores');

        $response->assertStatus(403);
    }
    public function test_tied_scores_are_preserved(): void
    {
        config(['app.hoppy_api_key' => 'test-key']);

        $attributes = ['score' => 100, 'origin' => 'phpunit', 'version' => 'test'];
        $attributes['level'] = 'Ranked1';
        Score::create(array_merge($attributes, ['name' => 'AAA']));
        Score::create(array_merge($attributes, ['name' => 'BBB']));

        $this->withHeaders(['x-api-key' => 'test-key', 'Accept' => 'application/json']);
        $response = $this->get('/api/v1/scores?level=Ranked1');

        $response->assertOk()->assertJsonCount(2, 'data');
        $this->assertEqualsCanonicalizing(['AAA', 'BBB'], array_column($response->json('data'), 'name'));
    }

}
