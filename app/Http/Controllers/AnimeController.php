<?php

namespace App\Http\Controllers;

use App\Models\Anime;
use Illuminate\Http\Request;
use App\Http\Resources\AnimeCollection;
use App\Http\Resources\AnimeResource;

class AnimeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $anime = Anime::paginate(10);
        return new AnimeCollection($anime);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $slug)
    {

        $anime = Anime::where('slug', $slug)->first();

        if (!$anime) {
            return response()->json([
                'error' => 'Anime not found',
                'status' => 400
            ], 400);
        }

        return new AnimeResource($anime);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
