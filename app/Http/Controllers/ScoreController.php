<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreScoreRequest;
use App\Http\Requests\UpdateScoreRequest;
use App\Models\v0\Score;

class ScoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $scores = Score::orderBy('score', 'desc')
            ->get();

        if ($scores) {

            $sorted = $this->sortStringScore($scores);

            return response()->json([
                'status' => 'OK',
                'code' => '200',
                'message' => 'List of scores',
                'data' => $sorted,
            ]);
        }

        return response()->json([
            'status' => 'Error',
            'code' => '500',
            'message' => 'Scores error',
        ]);
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreScoreRequest $request)
    {
        $score = Score::create([
            'name' => $request->get("name"),
            'score' => $request->get("score"),
            'origin' => $request->header('Origin'),
            // Prod is latest, for releases version is in domain when null
            'version' => $request->get("version") ? $request->get("version") : $request->header('Origin'),
        ]);

        if ($score) {
            // Return updated scores
            $scores = Score::orderBy('score', 'desc')
                ->get();

            if ($scores) {

                $sorted = $this->sortStringScore($scores);

                return response()->json([
                    'status' => 'OK',
                    'code' => '200',
                    'message' => 'List of scores',
                    'data' => $sorted,
                ]);
            }
        }

        return response()->json([
            'status' => 'Error',
            'code' => '500',
            'message' => 'Scores error',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Score $score)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Score $score)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateScoreRequest $request, Score $score)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Score $score)
    {
        //
    }
}
