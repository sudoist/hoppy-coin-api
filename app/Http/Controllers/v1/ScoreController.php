<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\GetScoresRequest;
use App\Http\Requests\v1\StoreScoreRequest;
use App\Http\Requests\v1\UpdateScoreRequest;
use App\Models\v1\Score;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ScoreController extends Controller
{
    private const GUEST_LEVELS = ['Phaser Initial', 'Arctic Classic', 'Land Mine Beach'];
    private const RUN_TTL_SECONDS = 1800;

    public function startRun(Request $request)
    {
        $validated = $request->validate(['level' => ['required', 'in:Phaser Initial,Arctic Classic,Land Mine Beach']]);
        $token = Str::random(64);
        Cache::put('hoppy-run:' . hash('sha256', $token), [
            'level' => $validated['level'],
            'started' => now()->timestamp,
        ], now()->addSeconds(self::RUN_TTL_SECONDS));

        return response()->json(['token' => $token, 'expires_in' => self::RUN_TTL_SECONDS]);
    }

    public function storeGuest(Request $request)
    {
        $data = $request->validate([
            'token' => ['required', 'string', 'size:64'],
            'name' => ['required', 'regex:/^[A-Za-z]{3}$/'],
            'score' => ['required', 'integer', 'min:10', 'max:1000000', 'multiple_of:10'],
            'level' => ['required', 'in:Phaser Initial,Arctic Classic,Land Mine Beach'],
        ]);

        $key = 'hoppy-run:' . hash('sha256', $data['token']);
        // Cache pull consumes the token, including failed submissions.
        $run = Cache::pull($key);
        if (!is_array($run) || $run['level'] !== $data['level']) {
            return response()->json(['message' => 'Invalid or expired run'], 422);
        }
        $elapsed = now()->timestamp - $run['started'];
        // Client scores are untrusted: reject impossible speed, not just token reuse.
        if ($elapsed < 2 || $elapsed > self::RUN_TTL_SECONDS || $data['score'] > $elapsed * 200) {
            return response()->json(['message' => 'Implausible run score'], 422);
        }

        Score::create([
            'name' => strtoupper($data['name']),
            'score' => $data['score'],
            'level' => $data['level'],
            'origin' => $request->header('Origin'),
            'version' => 'guest',
        ]);

        $scores = Score::where('level', $data['level'])->orderBy('score', 'desc')->get();
        return response()->json(['status' => 'OK', 'code' => '200', 'message' => 'List of scores', 'data' => $this->sortStringScore($scores)]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(GetScoresRequest $request)
    {
        $scores = Score::where('level', $request->get("level"))
            ->orderBy('score', 'desc')
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
            'level' => $request->get("level"),
            'origin' => $request->header('Origin'),
            // Prod is latest, for releases version is in domain when null
            'version' => $request->get("version") ? $request->get("version") : $request->header('Origin'),
        ]);

        if ($score) {
            // Return updated scores
            $scores = Score::where('level', $score->level)
                ->orderBy('score', 'desc')
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

    private function sortStringScore($scores)
    {
        $scoresCollection = collect([]);

        foreach ($scores as $score) {
            $scoresCollection->push([
                "name" => $score->name,
                "score" => $score->score,
                "level" => $score->level,
                "origin" => $score->origin,
                "version" => $score->version,
                "date" => $score->created_at,
            ]);
        }

        $sortedCollection = $scoresCollection->sortByDesc('score')->values();

        $sorted = [];

        // Converting back again to array, this is ugly but need to fix scores order for now
        foreach ($sortedCollection as $sortedScore) {
            $sorted[] = $sortedScore;
        }

        return $sorted;
    }
}
