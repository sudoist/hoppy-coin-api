<?php

namespace Tests\Unit;

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

    public function test_it_can_create_a_score()
    {
        // Create a new score
        $score = Score::create([
            "name" => "SDO",
            "score" => "1234",
        ]);

        // Assert that the score was created successfully
        $this->assertNotNull($score);
        $this->assertEquals($score->name, 'SDO');
        $this->assertEquals($score->score, '1234');
        $this->assertTrue($score->exists);
    }

    public function test_it_can_get_sorted_scores_in_descending_order()
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

        $scores = Score::orderBy('score', 'desc')
            ->get();

        if ($scores) {

            $sorted = $this->sortStringScore($scores);

            // Assert that the scores will be returned sorted by score descending
            $this->assertNotNull($sorted);
            $this->assertEquals($sorted[0]["name"], "DEV");
            $this->assertEquals($sorted[1]["name"], "SDO");
            $this->assertEquals($sorted[2]["name"], "AAA");
        }
    }

    private function sortStringScore($scores)
    {
        $scoresCollection = collect([]);

        foreach ($scores as $score) {
            $scoresCollection->put($score->score, [
                "name" => $score->name,
                "score" => $score->score,
                "date" => $score->created_at,
            ]);
        }

        $sortedCollection = $scoresCollection->sortBy([
            ['score', 'desc']
        ]);

        $sorted = [];

        // Converting back again to array, this is ugly but need to fix scores order for now
        foreach ($sortedCollection as $sortedScore) {
            $sorted[] =  $sortedScore;
        }

        return $sorted;
    }
}
