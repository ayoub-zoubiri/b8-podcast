<?php

namespace App\Http\Controllers;

use App\Models\Podcast;
use App\Models\Episode;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function searchPodcasts(Request $request)
    {
        $query = Podcast::query()->with('user');

        // Recherche par titre
        if ($request->has('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        // Recherche par catégorie
        if ($request->has('category')) {
            $query->where('category', 'like', '%' . $request->category . '%');
        }

        // Recherche par animateur
        if ($request->has('host')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->host . '%')
                  ->orWhere('prenom', 'like', '%' . $request->host . '%');
            });
        }

        $podcasts = $query->get();

        return response()->json($podcasts);
    }

    public function searchEpisodes(Request $request)
    {
        $query = Episode::query()->with('podcast');

        // Recherche par titre
        if ($request->has('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        // Recherche par podcast
        if ($request->has('podcast')) {
            $query->whereHas('podcast', function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->podcast . '%');
            });
        }

        // Recherche par date
        if ($request->has('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $episodes = $query->get();

        return response()->json($episodes);
    }
}
